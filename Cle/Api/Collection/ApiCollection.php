<?php namespace Cle\Api\Collection;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Cle\Api\Client as CLE;

abstract class ApiCollection extends Collection implements ApiCollectionInterface{

    protected $cle;

    public function getClient(){
        return $this->cle;
    }

    public function setClient(CLE $cle){
        return $this->cle = $cle;
    }    

    public abstract function fetchItems( array $params = [] );

    public abstract function isValid( $item );

    public abstract function save( array $params );

}