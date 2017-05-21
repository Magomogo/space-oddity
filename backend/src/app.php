<?php

$app = new Silex\Application();

$app->get('/', function () {
    return '<h1>Welcome to ACME pay!</h1>';
});

return $app;
