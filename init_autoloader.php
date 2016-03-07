<?php
$autoloader = require 'vendor/autoload.php';

Rollbar::init([
    'access_token' => getenv('ROLLBAR_KEY'),
    'environment' => 'development'
]);

return $autoloader;