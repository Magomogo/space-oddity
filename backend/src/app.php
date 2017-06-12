<?php
namespace Acme\Pay;

use function Acme\Pay\Asserts\assertValidDateOrNull;
use function Acme\Pay\Conversions\transactionToCsv;
use Doctrine\DBAL\Connection;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new Application();
$app->register(new ServiceProvider\CacheServiceProvider());
$app->register(new ServiceProvider\JsonValidatorServiceProvider());
$app->register(new ServiceProvider\EntitiesServiceProvider());
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'url'   => 'pgsql://acmepay:acmepay@127.0.0.1/acmepay',
    ),
));

$app->before(function () use ($app) {
    // default, but let's document it
    $app['db']->setTransactionIsolation(Connection::TRANSACTION_READ_COMMITTED);
});

$app->get('/', function () {
    return '<h1>Welcome to ACME pay!</h1>';
});

$app->get('/client', function () use ($app) {

    /** @var Service\Clients $clientsService */
    $clientsService = $app['clients-service'];

    return $app->json($clientsService->listOfClients());
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

$app->get(
    '/client/{clientName}/wallet/transactions',
    function ($clientName, HttpFoundation\Request $request) use ($app) {

        /** @var Service\Clients $clientsService */
        $clientsService = $app['clients-service'];
        try {
            $client = $clientsService->getByName($clientName);
        } catch (Exception\ClientDoesNotExists $e) {
            throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]));
        }

        /** @var Service\Transactions $transactionsService */
        $transactionsService = $app['transactions-service'];
        $list = $transactionsService->sortedList(
            $client,
            [
                'startDate' => assertValidDateOrNull($request->get('startDate')),
                'endDate' => assertValidDateOrNull($request->get('endDate')),
            ]
        );

        return in_array($request->headers->get('accept', 'text/csv'),  ['text/csv', '*/*'], true) ?
            $app->stream(
                function () use ($list) {
                    foreach ($list as $idx => $transaction) {
                        echo transactionToCsv($transaction, $idx === 0);
                    }
                },
                200,
                ['Content-Type' => 'text/csv; charset=UTF-8']
            )
            :
            $app->json($list);
    }
);

$app->get(
    '/client/{clientName}/wallet/summary',
    function ($clientName, HttpFoundation\Request $request) use ($app) {

        /** @var Service\Clients $clientsService */
        $clientsService = $app['clients-service'];
        try {
            $client = $clientsService->getByName($clientName);
        } catch (Exception\ClientDoesNotExists $e) {
            throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]));
        }

        /** @var Service\Transactions $transactionsService */
        $transactionsService = $app['transactions-service'];

        return $app->json($transactionsService->summary(
            $client,
            [
                'startDate' => assertValidDateOrNull($request->get('startDate')),
                'endDate' => assertValidDateOrNull($request->get('endDate')),
            ]
        ));
    }
);

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
        $date = assertValidDateOrNull($request->get('date'));
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

        try {
            /** @var Service\Transactions $transactionsService */
            $transactionsService = $app['transactions-service'];
            $transactionsService->create($fromWalletId, $toWalletId, $amount, $request->query->get('currency', 'own'));
        } catch (Exception\TransferPathIsNotFound $e) {
            throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]), $e);
        } catch (Exception\CurrencyRateUndefined $e) {
            throw new BadRequestHttpException(json_encode(['message' => $e->getMessage()]), $e);
        } catch (Exception\InsufficientBalance $e) {
            throw new BadRequestHttpException(json_encode(['message' => 'Not enough money']), $e);
        }

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
