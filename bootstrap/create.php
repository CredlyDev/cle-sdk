<?php

$basedir = dirname(dirname(__FILE__));

require $basedir.'/vendor/autoload.php';

$env = require($basedir.'/.env.php');

$cle = new Cle\Api\Client( $env['api_url'], $env );

$cle->credly()->authenticate(
    $env['username'], 
    $env['password']
);

$courses = $cle->courses();

$courses->addCourse($env['course_id'], sprintf('This is course: %s', $env['course_id']));

$courses->save();

//conenct into cle course
//retreive a token for that course
$cle->connect([
    'user_id'        => $env['user_id'],
    'integration_id' => $courses[$env['course_id']]->id
]);

return $cle;

//now perform all cle related tasks for course_id

//will fail if the current user is not manager of given app

