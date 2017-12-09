<?php namespace Cle\Api;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Cle\Api\Collection\GradableCollection;
use Cle\Api\Collection\StudentCollection;
use Cle\Api\Collection\RuleCollection;
use Cle\Api\Collection\CourseCollection;

class Client implements ClientInterface{

    use \Cle\Api\Traits\ClientTrait{
        __call as protected call;
    }

    protected $client;

    protected $tokens;

    protected $api;

    public function __construct( $url, array $config ){

        $this->setClient(
            static::initClient( $url, $config )
        );

        $this->api = static::initApi( $this->client, $this );
    }

    public function connect( array $params ){

        $res = $this->post('cle_auth/connect', [
            'auth' => 'oauth',
            'form_params' => [
                'user_id'        => $params['user_id'],
                'integration_id' => $params['integration_id']
            ]
        ]);

        $data = $res->data;

        $this->setTokens([
            'access_token'           => $data->token,
            'integration_account_id' => $data->connected_account_id,
        ]);

        return $this;
    }

    public function authenticate( array $params ){

        $res = $this->post('cle_auth/authenticate', [
            'auth' => 'oauth',
            'form_params' => [
                "user_id"      => $params['user_id'],
                "integration_id" => $params['integration_id'],
                "lis_person_contact_email_primary" => $params['email'],
                "roles" => $params['roles'],
                "tool_consumer_info_product_family_code" => "cle",
            ]
        ]);

        $data = $res->data;

        $this->setTokens([
            'access_token'           => $data->token,
            'integration_account_id' => $data->connected_account_id,
        ]);

        return $this;
    }

    public function setTokens( array $tokens ){
        $this->tokens = $tokens;
    }

    /**
     * performs request, or returns the api class matching function call
     *
     * @param      <type>  $fn      The function
     * @param      <type>  $params  The parameters
     *
     * @return     <type>  ( description_of_the_return_value )
     */

    public function __call($fn, $params){

        if(isset($this->api[$fn])){

            $api = $this->api[$fn];

            $api->setClient( $this );  

            if(isset($params[0])){
                $api->fetchItems( $params[0] );
            }

            return $api;
        }

        $params[1] = $this->addDefaultRequestParams( isset($params[1]) ? $params[1]: [] ); 

        return $this->call($fn, @$params);
    }

    /**
     * Adds default request parameters.
     *
     * @param      array  $params  The parameters
     */

    protected function addDefaultRequestParams( array $params ){

        if($this->tokens) $params['query'] = array_merge(
            isset($params['query']) ? $params['query'] : [],
            $this->tokens
        ); 

        return $params;
    }

    /**
     * get the api callback classes
     *
     * @param      <type>  $client  The client
     *
     * @return     array   ( description_of_the_return_value )
     */

    protected static function initApi( $client, $cle ){

        return [
            'students'  => new StudentCollection([]),
            'gradable'  => new GradableCollection([]),
            'rules'     => new RuleCollection([]),
            'courses'   => new CourseCollection([]),
            'credly'    => new CredlyClient(),
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