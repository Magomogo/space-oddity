<?php

use Acme\Pay\ServiceProvider\JsonValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app->register(new JsonValidatorServiceProvider());

$app->get('/', function () {
    return '<h1>Welcome to ACME pay!</h1>';
});

$app->post('/client', function (Request $request) use ($app) {
    /** @var \Acme\Pay\Service\JsonValidator $jsonValidator */
    $jsonValidator = $app['data_types_validator'];

    $newClient = $jsonValidator->assertValid(
        json_decode($request->getContent()),
        'http://acmepay.local/schema/client.json'
    );

    return $app->json([], 201);
});

$app->error(function (\Symfony\Component\HttpKernel\Exception\BadRequestHttpException $e) use ($app) {
    return $app->json(json_decode($e->getMessage()), 400);
});

return $app;
