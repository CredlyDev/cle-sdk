<?php namespace Cle\Api\Traits;

use GuzzleHttp\Client as Guzzle;

trait ClientTrait{

    public function __call( $fn, $params ){

        $params = array_values($params);

        if(count($params) < 2) 
            throw new \InvalidArgumentException(
                sprintf("Missing parameters in '%s' request", $fn)
            );

        return $this->makeRequest( $fn, $params[0], $params[1] );
    }

    public function makeRequest( $method, $endpoint, $params ){

        if(!in_array(strtolower($method), ['post', 'get', 'put', 'delete', 'update'])){
            throw new \InvalidArgumentException(
                sprintf("Unsupported request method '%s' ", $method)
            );
        }

        $res = $this->client->{$method}($endpoint, $params);

        return json_decode($res->getBody());
    }

    public function setClient( Guzzle $client ){

        $this->client = $client;

        return $this;
    }

    public function getClient(){

        return $this->client;
    }    

    public function setTokens(array $tokens){

        $this->tokens = $tokens;

        return $this;
    }
}