<?php 


use GuzzleHttp\TransferStats;

$env    = require('.env.php');

$base = require('base.php');

if(!empty($_POST)){

    $client = $base($env['enterprise_url']);

    //define url
    $url = false;

    //post lti parameters to enterprise lti endpoint
    //with oauth signature 

    $res = $client->post('auth/lti', [
        'auth' => 'oauth',
        'form_params' => [
            "user_id"        => @$_POST['user_id'] ?: $env['user_id'],
            "integration_id" => @$_POST['integration_id'] ?: $env['integration_id'], 
            "lis_person_contact_email_primary" => @$_POST['email'] ?: $env['email'],
            "roles" => "LEARNER",
            "lis_person_name_given" => $_POST['first_name'],
            "lis_person_name_family" => $_POST['last_name'],
            "context_id" => uniqid(),
            "context_label" => "CLE Integrated",
            "tool_consumer_info_product_family_code" => "cle",
        ],
        'on_stats' => function (TransferStats $stats) use (&$url) {
            //retrieve the redirect url
            $url = $stats->getEffectiveUri();
        }
    ]);

    //if redirect url provided 
    //follow the redirect to enterprise to finish authorizing app
    if($url){   
        header("Location: $url");
    }
} else {

    $client = $base($env['api_url']);

    //attempt to authenticate using oauth signed oauth request

    $res = $client->post('cle_auth/authenticate', [
        'auth' => 'oauth',
        'form_params' => [
            "user_id"      => $env['user_id'],
            "integration_id" => $env['integration_id'],
            "lis_person_contact_email_primary" => $env['email'],
            "roles" => "ADMINISTRATOR",
            "tool_consumer_info_product_family_code" => "cle",
        ]
    ]);

    $data = json_decode($res->getBody());

    $res = $client->get('cle_integrations', [
        'query' => [
            'access_token' => $data->data->token,
            'integration_account_id' => $data->data->connected_account_id,
        ]
    ]);

    $integrations = json_decode($res->getBody())->data;
}

require ('views/student.php');

