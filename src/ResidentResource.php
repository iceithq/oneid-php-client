<?php

namespace OneID;

class ResidentResource
{
    private Client $client;

    function __construct($client)
    {
        $this->client = $client;
    }

    function hello()
    {
        return $this->client->get('/resident/hello');
    }

    function get_real_properties()
    {
        return $this->client->get('/resident/properties');
    }

    function get_real_property($property_id)
    {
        return $this->client->get('/resident/properties/' . $property_id);
    }

    function pay_real_property($property_id, $data)
    {
        return $this->client->post_json('/resident/properties/' . $property_id . '/pay', $data);
    }

    function update_avatar($avatar_url)
    {
        $data = array('avatar_url' => $avatar_url);
        return $this->client->post_json('/resident/update_avatar', $data);
    }

    function login($username, $password)
    {
        $data = array('username' => $username, 'password' => $password);
        return $this->client->post_json('/auth/resident/login', $data);
    }

    function verify($code)
    {
        return $this->client->post_json('/auth/resident/verify', ['code' => $code]);
    }

    function register($resident)
    {
        return $this->client->post_json('/auth/resident/register', $resident);
    }

    function get_news($news_id = null)
    {
        $data = [];
        return $this->client->get('/resident/news/' . $news_id, $data);
    }

    function get_external_services()
    {
        $data = [];
        return $this->client->get('/resident/external_services', $data);
    }

    function get_financial_aid()
    {
        $data = [];
        return $this->client->get('/resident/programs/financial', $data);
    }

    function get_applications()
    {
        $data = [];
        return $this->client->get('/resident/applications', $data);
    }

    function get_disaster_applications()
    {
        $data = [];
        return $this->client->get('/resident/applications/disaster', $data);
    }

    function get_reliefs()
    {
        $data = [];
        return $this->client->get('/resident/programs/relief', $data);
    }

    function get_disasters()
    {
        $data = [];
        return $this->client->get('/resident/programs/disaster', $data);
    }

    function get_certificates()
    {
        $data = [];
        return $this->client->get('/resident/certificates', $data);
    }

    function get_certificate_applications()
    {
        $data = [];
        return $this->client->get('/resident/certificates/applications', $data);
    }

    function get_certificate($certificate_id)
    {
        $data = [];
        return $this->client->get('/resident/certificates/' . $certificate_id, $data);
    }

    function apply_certificate($certificate_id, $application)
    {
        return $this->client->post_json('/resident/certificates/' . $certificate_id . '/apply', $application);
    }

    function download_certificate_application($certification_application_id)
    {
        return $this->client->get_binary('/resident/certificates/applications/' . $certification_application_id . '/download');
    }

    function claim_program($program_id)
    {
        $data = [];
        return $this->client->get('/resident/programs/' . $program_id . '/claim', $data);
    }

    function get_program($program_id)
    {
        $data = [];
        return $this->client->get('/resident/programs/' . $program_id, $data);
    }

    function get_announcements()
    {
        $data = [];
        return $this->client->get('/resident/announcements', $data);
    }

    function get_notifications()
    {
        $data = [];
        return $this->client->get('/resident/notifications', $data);
    }

    function claim($program_id, $claim)
    {
        return $this->client->post_json('/resident/programs/' . $program_id . '/claim', $claim);
    }

    function get_wallet()
    {
        return $this->client->get('/resident/wallet');
    }

    function withdraw($transaction)
    {
        return $this->client->post_json('/resident/wallet/withdraw', $transaction);
    }

    function get_transactions()
    {
        return $this->client->get('/resident/transactions');
    }

    function get_transaction($reference_no)
    {
        return $this->client->get('/resident/transactions/' . $reference_no);
    }

    function get_profile()
    {
        return $this->client->get('/resident/profile');
    }
}
