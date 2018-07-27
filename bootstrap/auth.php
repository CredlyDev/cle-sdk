<?php 

$basedir = dirname(dirname(__FILE__));

require $basedir.'/vendor/autoload.php';

$env = require($basedir.'/.env.php');

$client = require(dirname(__FILE__).'/create.php');

$courses = $client->courses();

$client->authenticate([
    'user_id'        => $env['user_id'],
    'integration_id' => $courses[$env['course_id']]->id,
    'email'          => $env['email'],
    'roles'          => 'ADMINISTRATOR',
]);

return $client;