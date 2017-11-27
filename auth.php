<?php 

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use CLE\OAuth1\OAuth1 as OAuth;

$env   = require('.env.php');

$base  = require('base.php');

$client = $base($env['api_url']);

//attempt to authenticate using oauth signed oauth request
//requires an existing user_id and integration_id
//key/secret must be of the app that created the integration 
//or of an app that is authorized to manage the integration
//in order to recieve a token this way

$res = $client->post('cle_auth/authenticate', [
    'auth' => 'oauth',
    'form_params' => [
        "user_id"      => $env['user_id'],
        "integration_id" => $env['integration_id'],
        "lis_person_contact_email_primary" => $env['email'],
        "roles" => "ADMINISTRATOR",
        "tool_consumer_info_product_family_code" => "cle",
    ]
]);

//grab the token response
$data = json_decode($res->getBody())->data;

//if the token owner is authorized to manage integrations
//the owner can use it's access_token to authorize another app 
//to use any integration the owner manages

$res = $client->post('cle_auth/authorize', [
    'query' => [
        'access_token' => $data->token
    ],
    'form_params' => [
        //the key of the new app to authorize
        'oauth_consumer_key' => $env['oauth_consumer_key'],
        'redirect_url'       => 'http://localhost:3000',
        'integration_ids'    => $env['integration_id']
    ] 
]);

$data = json_decode($res->getBody())->data;

//if successful authorize request will return a code
//we can exhange for an access token for user on another app 

$res = $client->post('cle_auth/exchange', [
    'form_params' => [
        'code' => $data->code,
    ] 
]);

//returns token for user on the requesting app,
//along with a list of cle instances user is authorized to manage

var_dump(json_decode($res->getBody()));

