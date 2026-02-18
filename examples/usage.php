<?php

require_once __DIR__ . '/../vendor/autoload.php';
// $api_url = 'https://oneid.systems/api/v1';
$api_url = 'http://localhost:8001/api/v1';
$client = new \OneID\Client($api_url, true);
// $r = $client->login('mayor@biringancity.gov.ph', 'password');
$r = $client
    // ->token($r->token)
    ->get_cameras();
echo $r;
