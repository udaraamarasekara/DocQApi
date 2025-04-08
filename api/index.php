<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Use Laravel's routing
$response = $app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

$response->send();

$app->terminate($request, $response);
