#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$faker = Faker\Factory::create();

echo $faker->name . "\n";
echo $faker->country . "\n";
echo $faker->city . "\n";
