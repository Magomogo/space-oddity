#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$pool = new Stash\Pool(new \Stash\Driver\Memcache());
$pool->deleteItem('acmepay');
