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

return function($url) use ($env, $stack){
    return new Client([
        'base_uri' => $url,
        'handler'  => $stack,
        'headers'  => $env['headers']
    ]);
};