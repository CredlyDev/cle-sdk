<?php 

$basedir = dirname(dirname(__FILE__));

require $basedir.'/vendor/autoload.php';

$env = require($basedir.'/.env.php');

$client = new Cle\Api\Client( $env['api_url'], $env );

$client->authenticate([
    'user_id'        => $env['user_id'],
    'integration_id' => $env['integration_id'],
    'email'          => $env['email'],
    'roles'          => 'ADMINISTRATOR',
]);

return $client;