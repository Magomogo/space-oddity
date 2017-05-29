<?php
namespace Acme\Pay;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new Application();
$app->register(new ServiceProvider\JsonValidatorServiceProvider());
$app->register(new ServiceProvider\EntitiesServiceProvider());
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'url'   => 'pgsql://acmepay:acmepay@127.0.0.1/acmepay',
    ),
));

$app->get('/', function () {
    return '<h1>Welcome to ACME pay!</h1>';
});

$app->post('/client', function (HttpFoundation\Request $request) use ($app) {
    /** @var Service\JsonValidator $jsonValidator */
    $jsonValidator = $app['data-types-validator'];

    $newClient = $jsonValidator->assertValid(
        json_decode($request->getContent()),
        'http://acmepay.local/schema/client.json'
    );

    /** @var Service\Clients $clientsService */
    $clientsService = $app['clients-service'];
    try {

        return $app->json(
            $clientsService->create($newClient),
            201,
            ['Location' => '/client/' . rawurlencode($newClient->name)]
        );

    } catch (Exception\ClientAlreadyExists $e) {
        throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]));
    }

});

$app->post(
    '/client/{clientName}/wallet/{currencyCode}',
    function ($clientName, $currencyCode, HttpFoundation\Request $request) use ($app) {

        /** @var Service\Clients $clientsService */
        $clientsService = $app['clients-service'];
        try {
            $client = $clientsService->getByName($clientName);
        } catch (Exception\ClientDoesNotExists $e) {
            throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]));
        }

        /** @var Service\JsonValidator $jsonValidator */
        $jsonValidator = $app['data-types-validator'];
        $walletCurrency = $jsonValidator->assertValid(
            $currencyCode,
            'http://acmepay.local/schema/currency.json'
        );

        /** @var Service\Wallets $walletsService */
        $walletsService = $app['wallets-service'];
        $wallet = $walletsService->create($client, $walletCurrency, (int)$request->get('balance', 0));

        return $app->json(
            $wallet,
            201,
            ['Location' => '/wallet/' . $wallet->id]
        );
    }
);

$app->post('/currency/{code}/rate/{rate}', function ($code, $rate, HttpFoundation\Request $request) use ($app) {

    /** @var Service\JsonValidator $jsonValidator */
    $jsonValidator = $app['data-types-validator'];
    $currency = $jsonValidator->assertValid(
        $code,
        'http://acmepay.local/schema/currency.json'
    );

    if ($rate === 0.0) {
        throw new BadRequestHttpException(json_encode(['message' =>'Currency rate cannot be zero']));
    }

    if ($request->query->has('date')) {
        try {
            $date = (new \DateTime($request->get('date')))->format('Y-m-d');
        } catch (\Exception $e) {
            throw new BadRequestHttpException(json_encode(['message' => 'Invalid date']));
        }
    } else {
        $date = (new \DateTime('tomorrow'))->format('Y-m-d');
    }

    try {
        /** @var Service\Currencies $currenciesService */
        $currenciesService = $app['currencies-service'];
        $currenciesService->defineRate($currency, $rate, $date);
    } catch (Exception\CurrencyRateForThisDateIsAlreadyDefined $e) {
        throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]));
    }

    return $app->json(['message' => 'created'], 201);
})->convert(
    'rate',
    function ($rate) {
        return (float)$rate;
    }
);

$app->put(
    '/wallet/{fromWalletId}/transfer-to/{toWalletId}/amount/{amount}',
    function ($fromWalletId, $toWalletId, $amount, HttpFoundation\Request $request) use ($app) {

        if ($amount <= 0) {
            throw new BadRequestHttpException(json_encode(['message' =>'Amount should be more than zero']));
        }

        if ($request->query->has('currency') && !in_array($request->query->get('currency'), ['own', 'their'], true)) {
            throw new BadRequestHttpException(json_encode(['message' =>'Transfer can be done in own or their currency']));
        }

        if ($fromWalletId === $toWalletId) {
            throw new BadRequestHttpException(json_encode(['message' =>'Wallets should be different']));
        }

        /** @var Service\Wallets $walletsService */
        $walletsService = $app['wallets-service'];
        $walletsService->transfer($fromWalletId, $toWalletId, $amount, $request->query->get('currency', 'own'));

        return $app->json(['message' => 'transferred']);
    }
)->convert(
    'amount',
    function ($amount) {
        return (int)$amount;
    }
);

$app->error(function (BadRequestHttpException $e) use ($app) {
    return $app->json(json_decode($e->getMessage()), 400);
});

$app->error(function (\Exception $e) {
    if (defined('TEST_MODE')) {
        echo (string)$e;
    }

    return HttpFoundation\Response::create((string)$e, 500);
});

$app->after(function (HttpFoundation\Request $request, HttpFoundation\Response $response) {
    if($response->headers->get('Content-type') === 'application/json') {
        $response->setContent(
            json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT)
            . "\n"
        );
    }
});

return $app;
