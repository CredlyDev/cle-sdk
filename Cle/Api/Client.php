<?php namespace Cle\Api;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Cle\Api\Collection\GradableCollection;
use Cle\Api\Collection\StudentCollection;
use Cle\Api\Collection\RuleCollection;

class Client{

    protected $client;

    protected $token;

    protected $integrationAccountId;

    protected $api;

    public function __construct( $url, array $config ){

        $this->client = static::initClient( $url, $config );

        $this->api = static::initApi( $this->client );
    }

    public function authenticate( array $params ){

        $res = $this->client->post('cle_auth/authenticate', [
            'auth' => 'oauth',
            'form_params' => [
                "user_id"      => $params['user_id'],
                "integration_id" => $params['integration_id'],
                "lis_person_contact_email_primary" => $params['email'],
                "roles" => $params['roles'],
                "tool_consumer_info_product_family_code" => "cle",
            ]
        ]);

        $data = json_decode($res->getBody())->data;

        $this->token = $data->token;

        $this->integrationAccountId = $data->connected_account_id;

        return $this;
    }

    public function __call($fn, $params){

        if(isset($this->api[$fn])){

            $api = $this->api[$fn];

            $api->setClient( $this->client );            

            $api->setTokens([
                'access_token' => $this->token,
                'integration_account_id' => $this->integrationAccountId
            ]);

            if(isset($params[0])){
                $api->fetchItems( $params[0] );
            }

            return $api;
        }

        throw new \BadMethodCallException(sprintf("Call to undefined method %s", $fn));
    }

    protected static function initApi( $client ){

        return [
            'students'  => new StudentCollection([]),
            'gradable'  => new GradableCollection([]),
            'rules'     => new RuleCollection([]),
        ];
    }

    protected static function initClient( $url, $config ){

        $oauth = static::makeOauthHandler($config);

        $stack = static::addHandlers(
            [$oauth],
            $config
        );

        return static::makeClientInstance( $url, $stack, $config );
    }

    protected static function makeOauthHandler( array $config ){

        return new Oauth1([
            'consumer_key'    => $config['oauth_consumer_key'],
            'consumer_secret' => $config['oauth_consumer_secret'],
            'token'           => '',
            'token_secret'    => '',
        ]);
    }

    protected static function makeHandlerStack( array $config ){

        return HandlerStack::create();
    }

    protected static function addHandlers( array $handlers, array $config ){

        $handlerStack = static::makeHandlerStack( $config );

        foreach($handlers as $h) $handlerStack->push($h);

        return $handlerStack;
    }

    protected static function makeClientInstance( $url, HandlerStack $stack, array $config ){

        return new Guzzle([
            'base_uri' => $url,
            'handler'  => $stack,
            'headers'  => $config['headers']
        ]);
    }
}