<?php

use Symfony\Component\HttpFoundation;
use Acme\Pay\ServiceProvider;
use Acme\Pay\Service;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new Silex\Application();
$app->register(new ServiceProvider\JsonValidatorServiceProvider());
$app->register(new ServiceProvider\ClientsServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
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
        $clientsService->create($newClient);
    } catch (\Acme\Pay\Exception\ClientAlreadyExists $e) {
        throw new BadRequestHttpException(json_encode($e->getMessage()));
    }

    return $app->json([], 201);
});

$app->error(function (BadRequestHttpException $e) use ($app) {
    return $app->json(json_decode($e->getMessage()), 400);
});

$app->error(function (\Exception $e) {
    if (defined('TEST_MODE')) {
        echo (string)$e;
    }

    return HttpFoundation\Response::create((string)$e, 500);
});

return $app;
