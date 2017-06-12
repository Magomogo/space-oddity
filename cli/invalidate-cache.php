#!/usr/bin/env php
<?php

require __DIR__ . '/../backend/vendor/autoload.php';

$pool = new Stash\Pool(new \Stash\Driver\Memcache());
$pool->deleteItem('acmepay');
