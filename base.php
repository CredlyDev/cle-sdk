<?php 

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use CLE\OAuth1\OAuth1 as OAuth;

$stack = HandlerStack::create();

$middleware = new Oauth1([
    'consumer_key'    => $env['oauth_consumer_key'],
    'consumer_secret' => $env['oauth_consumer_secret'],
    'token'           => '',
    'token_secret'    => '',
]);

$stack->push($middleware);

/**
 * attempt to fetch token via CLE authentication
 *
 * @param      \GuzzleHttp\Client  $client  The client
 * @param      array               $env     The environment
 *
 * @return     <type>              ( description_of_the_return_value )
 */

function authenticate( Client $client, array $env ){

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

    return json_decode($res->getBody());
}


return function($url) use ($env, $stack){
    return new Client([
        'base_uri' => $url,
        'handler'  => $stack,
        'headers'  => $env['headers']
    ]);
};