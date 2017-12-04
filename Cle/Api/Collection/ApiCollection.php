<?php namespace Cle\Api\Collection;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

abstract class ApiCollection extends Collection implements ApiCollectionInterface{

    protected $client;

    public function setTokens(array $tokens){
        $this->tokens = $tokens;
    }

    public function setClient( Client $client ){

        $this->client = $client;

        return $this;
    }

    public function getClient(){

        return $this->client;
    }

    public abstract function fetchItems( array $params = [] );

    public abstract function isValid( $item );

    public abstract function save( array $params );

}