#!/usr/bin/env php
<?php

require __DIR__ . '/../backend/vendor/autoload.php';

use Symfony\Component\HttpKernel\Client;

$httpClient = new Client(require __DIR__ . '/../backend/src/app.php');

$ensureCurrencyRatesAreDefined = function () use ($httpClient)
{
    $today = date('Y-m-d');

    $httpClient->request('POST', "/currency/USD/rate/1?date=$today");

    if($httpClient->getResponse()->getStatusCode() === 500) {
        die($httpClient->getResponse()->getContent());
    }

    $httpClient->request('POST', "/currency/RUB/rate/56.57?date=$today");

    if($httpClient->getResponse()->getStatusCode() === 500) {
        die($httpClient->getResponse()->getContent());
    }
};

$createClient = function ($client, $currency = 'USD') use ($httpClient)
{
    $httpClient->request('POST', '/client', [], [], [], json_encode($client));

    if($httpClient->getResponse()->getStatusCode() !== 201) {
        die($httpClient->getResponse()->getContent());
    }

    echo sprintf("Created client %s from %s, %s\n", $client->name, $client->city, $client->country);

    $balance = mt_rand(10000, 100000);

    $httpClient->request(
        'POST',
        '/client/' . rawurlencode($client->name) . "/wallet/$currency?balance=" . $balance
    );

    if($httpClient->getResponse()->getStatusCode() !== 201) {
        die($httpClient->getResponse()->getContent());
    }

    echo sprintf("... having USD %0.2f in wallet\n", $balance/100);

    return json_decode($httpClient->getResponse()->getContent());
};

$transfer = function ($wallet1, $wallet2, $currency = 'own') use ($httpClient)
{
    $amount = mt_rand(100, 10000);

    $httpClient->request('PUT', "/wallet/$wallet1->id/transfer-to/$wallet2->id/amount/$amount?currency=$currency");

    if($httpClient->getResponse()->getStatusCode() !== 200) {
        die($httpClient->getResponse()->getContent());
    }

    echo sprintf("Transferred USD %0.2f from %s to %s\n", $amount/100, $wallet1->client->name, $wallet2->client->name);
};

$faker = Faker\Factory::create();

$ensureCurrencyRatesAreDefined();

$wallet1 = $createClient((object)[
    'name' => $faker->name,
    'country' => $faker->country,
    'city' => $faker->city,
]);

$wallet2 = $createClient((object)[
    'name' => $faker->name,
    'country' => $faker->country,
    'city' => $faker->city,
], 'RUB');

$transfer($wallet1, $wallet2);
$transfer($wallet1, $wallet2, 'their');
$transfer($wallet1, $wallet2);
$transfer($wallet2, $wallet1);
