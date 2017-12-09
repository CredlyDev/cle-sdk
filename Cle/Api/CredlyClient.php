<?php namespace Cle\Api;

use Cle\Api\Client as CLE;

class CredlyClient{

    protected $cle;

    public function setClient( CLE $cle ){
        $this->cle = $cle;

        return $this;
    }

    public function getClient(){
        return $this->cle;
    }

    public function authenticate($username, $password){

        $res = $this->getClient()->post('authenticate',[
            'auth' => [
                $username, 
                $password
            ]
        ]);

        $this->cle->setTokens([
            'access_token' => $res->data->token 
        ]);

        return $this;
    }

}

