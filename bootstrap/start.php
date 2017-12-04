<?php

$dir = dirname(__FILE__);

$client = isset($client) ? $client : require($dir.'/auth.php');

require($dir.'/gradable.php');

require($dir.'/students.php');

require($dir.'/rules.php');