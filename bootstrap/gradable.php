<?php 

$client = isset($client) ? $client : require(dirname(__FILE__).'/auth.php');

$gradables = $client->gradable();

$gradables->put('21ff23321s23e', (object) [
    'name'                 => 'Math Studies 2',
    'callback'             => 'letterGrade',
    'min_pass_requirement' => 'D',
    'extern_id'            => '21ff23321s23e',
]);

$gradables->put('21ff2332123e', (object) [
    'name'                 => 'English Studies 2',
    'callback'             => 'letterGrade',
    'min_pass_requirement' => 'D',
    'extern_id'            => '21ff2332123e',
]);

$gradables->save();