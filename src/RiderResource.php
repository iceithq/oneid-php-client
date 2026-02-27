<?php

namespace OneID;

class RiderResource
{
    private Client $client;

    function __construct($client)
    {
        $this->client = $client;
    }

    function hello()
    {
        return $this->client->get('/rider/hello');
    }

    function get_jobs()
    {
        $data = [];
        return $this->client->get('/rider/jobs', $data);
    }

    function accept_job($job_id)
    {
        $data = array();
        return $this->client->post_json('/rider/jobs/' . $job_id . '/accept', $data);
    }
}
