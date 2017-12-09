<?php namespace Cle\Api;

use GuzzleHttp\Client as Guzzle;

interface ClientInterface{

    public function setClient( Guzzle $client );

    public function getClient();

    public function setTokens( array $tokens );
}