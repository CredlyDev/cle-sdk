<?php 


use GuzzleHttp\TransferStats;

$env    = require('.env.php');

$base = require('base.php');

$client = $base($env['enterprise_url']);

//define url
$url = false;

//post lti parameters to enterprise lti endpoint
//with oauth signature 

$res = $client->post('auth/lti', [
    'auth' => 'oauth',
    'form_params' => [
        "user_id"        => $env['user_id'],
        "integration_id" => $env['integration_id'], 
        "lis_person_contact_email_primary" => $env['email'],
        "roles" => "ADMINISTRATOR",
        "tool_consumer_info_product_family_code" => "cle",
    ],
    'on_stats' => function (TransferStats $stats) use (&$url) {
        //retrieve the redirect url
        $url = $stats->getEffectiveUri();
    }
]);

//if redirect url provided 
//follow the redirect to enterprise to finish authorizing app
if($url){
    header("Location: $url");
}



