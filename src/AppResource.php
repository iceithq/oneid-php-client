<?php

/**
 * OneID
 *
 * Unified Digital Identity Platform for Local Government Units (LGUs)
 *
 * Enables residents to securely access government services, disaster
 * assistance, and municipal information across cities and municipalities.
 */

namespace OneID;

class AppResource
{
    private Client  $client;
    private string $app_key;
    var $full_encoded_app_key;
    var $endpoint;

    function __construct($client, $app_key)
    {
        $this->client = $client;
        $this->app_key = $app_key;
    }

    function endpoint($endpoint)
    {
        $encoded_app_key = urlencode($this->app_key);
        $this->full_encoded_app_key = str_replace('.', '%2E', $encoded_app_key);
        $this->endpoint = $endpoint;
        return $this;
    }

    function get()
    {
        return $this->client->get('/apps/' . $this->full_encoded_app_key . '/' . urlencode($this->endpoint));
    }
}
