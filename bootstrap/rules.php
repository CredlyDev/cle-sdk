<?php

$client = isset($client) ? $client : require(dirname(__FILE__).'/auth.php');

$env = require (dirname(dirname(__FILE__)).'/.env.php');

//set rule badge
$rules = $client->rules()->setBadgeId($env['badge_id']);

$gradable = $client->gradable()->fetchItems();

//describe the rules
$rules->setDesc("An exmaple description for badge rules");

//add rules with requirements
$rules->addRule('test_rule', function($rule) use($gradable){

    $rule->addRequirement( $gradable[0]->extern_id, 'greaterThan', 'C' )
         ->addRequirement( $gradable[1]->extern_id, 'greaterThanOrEqualTo', 'B' );
});



$rules->save();