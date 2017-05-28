<?php

use Symfony\Component\HttpFoundation\Request;
use Acme\Pay\ServiceProvider;
use Acme\Pay\Service;

$app = new Silex\Application();
$app->register(new ServiceProvider\JsonValidatorServiceProvider());
$app->register(new ServiceProvider\ClientsServiceProvider());

$app->get('/', function () {
    return '<h1>Welcome to ACME pay!</h1>';
});

$app->post('/client', function (Request $request) use ($app) {
    /** @var Service\JsonValidator $jsonValidator */
    $jsonValidator = $app['data-types-validator'];

    $newClient = $jsonValidator->assertValid(
        json_decode($request->getContent()),
        'http://acmepay.local/schema/client.json'
    );

    /** @var Service\Clients $clientsService */
    $clientsService = $app['clients-service'];
    $clientsService->create($newClient);

    return $app->json([], 201);
});

$app->error(function (\Symfony\Component\HttpKernel\Exception\BadRequestHttpException $e) use ($app) {
    return $app->json(json_decode($e->getMessage()), 400);
});

return $app;
