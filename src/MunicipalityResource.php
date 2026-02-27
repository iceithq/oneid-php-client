<?php

namespace OneID;

class MunicipalityResource
{
    var $client;

    function __construct($client)
    {
        $this->client = $client;
    }

    function hello()
    {
        return $this->client->get('/municipality/hello');
    }

    function get_news($news_id = null, $municipality_id = null)
    {
        $data = [];
        return $this->client->get('/municipality/news/' . $news_id, $data);
    }
}
