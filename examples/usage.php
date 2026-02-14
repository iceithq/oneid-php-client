<?php

require_once __DIR__ . '/../vendor/autoload.php';
$api_url = 'https://oneid.systems/api/v1';
$client = new \OneID\Client($api_url);
$r = $client->login('mayor@biringancity.gov.ph', 'password');
\OneID\print_pre($r);
