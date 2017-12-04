<?php namespace Cle;

use Illuminate\Support\Collection;

class Collection extends BaseCollection{

    protected $callbacks;

    public function setItemValidationCallback($callback){

        $this->callbacks['validation'] = $callback;

        return $this;
    }

    public function setCollectionSaveMethod($callback){

        $this->callbacks['save'] = $callback;

        return $this;
    }

    public function save()

} 