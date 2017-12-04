<?php namespace Cle\Api\Collection;

interface ApiCollectionInterface{

    public function getClient();

    public function isValid( $item );

    public function fetchItems( array $params = [] );

    public function save( array $params );

}