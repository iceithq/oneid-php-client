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

    function get_job($job_id)
    {
        $data = [];
        return $this->client->get('/rider/jobs/' . $job_id, $data);
    }

    function accept_job($job_id)
    {
        $data = array();
        return $this->client->post_json('/rider/jobs/' . $job_id . '/accept', $data);
    }

    function get_profile()
    {
        return $this->client->get('/rider/profile');
    }
}
