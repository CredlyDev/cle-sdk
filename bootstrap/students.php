<?php 

$client = isset($client) ? $client : require(dirname(__FILE__).'/auth.php');

$students = $client->rules();

$students->put('123', (object) [
    'first'     => 'John',
    'last'      => 'Doe',
    'email'     => 'jon.doe@gmail.com',
    'extern_id' => '123',
]);

$students->put('456', (object) [
    'first'     => 'Jane',
    'last'      => 'Doe',
    'email'     => 'jane.doe@gmail.com',
    'extern_id' => '456',
]);

$students->save();