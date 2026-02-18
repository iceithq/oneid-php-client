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

// defined('BASEPATH') or exit('No direct script access allowed');

class Client
{
    var $base_url;
    var $token;
    var $app_debug;

    function __construct($base_url = null, $app_debug = null)
    {
        $this->app_debug = $app_debug;
        $this->init($base_url);
    }

    function init($base_url)
    {
        $this->base_url = $base_url;
    }

    function token($token)
    {
        $this->token = $token;
        return $this;
    }

    function resident()
    {
        return new ResidentResource($this);
    }

    function municipality()
    {
        return new MunicipalityResource($this);
    }

    function rider()
    {
        return new RiderResource($this);
    }

    function get_genders()
    {
        return $this->get('/genders');
    }

    function get_cameras()
    {
        return $this->get('/cameras');
    }

    function get_civil_statuses()
    {
        return $this->get('/civil_statuses');
    }

    function get_municipality($municipality_id = null)
    {
        $data = [];
        $municipality_id = $municipality_id;
        return $this->get('/municipalities/' . $municipality_id, $data);
    }

    function get_municipalities($province_id = null)
    {
        $data['province_id'] = $province_id;
        return $this->get('/municipalities', $data);
    }

    function get_barangays($municipality_id = null)
    {
        $data = [];
        $municipality_id = $municipality_id;
        return $this->get('/municipalities/' . $municipality_id . '/barangays', $data);
    }

    function get_program_statuses()
    {
        $data = [];
        return $this->get('/programs/statuses', $data);
    }

    function get_certificate_purposes()
    {
        $data = [];
        return $this->get('/certificates/purposes', $data);
    }

    function get_disaster_types()
    {
        $data = [];
        return $this->get('/disasters/types', $data);
    }

    function login($username, $password)
    {
        $data = array('username' => $username, 'password' => $password);
        return $this->post_json('/auth/login', $data);
    }

    function get_jobs()
    {
        $data = [];
        return $this->get('/jobs', $data);
    }

    function accept_job($job_id)
    {
        $data = array();
        return $this->post_json('/jobs/' . $job_id . '/accept', $data);
    }

    // Private functions
    function get($url, $data = [], $headers = [])
    {
        $curl = curl_init();

        // Build query string
        $params = $this->base_url . $url;
        if (!empty($data)) {
            $params .= '?' . http_build_query($data);
        }

        // Default headers
        $default_headers = [
            'Accept: application/json',
            'Content-Type: application/json', // optional for GET
        ];

        // Auto-attach Bearer token if present
        if (!empty($this->token)) {
            $default_headers[] = 'Authorization: Bearer ' . $this->token;
        }

        // Merge custom headers
        $final_headers = !empty($headers) ? array_merge($default_headers, $headers) : $default_headers;

        // Debug URL
        if ($this->app_debug) {
            echo 'curl -X GET "' . $params . '" ';
            foreach ($final_headers as $h) {
                echo '  -H "' . $h . '" ';
            }
            echo endl();
        }

        // cURL options
        curl_setopt($curl, CURLOPT_URL, $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $final_headers);

        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            // log_message('error', 'cURL Error: ' . $error_msg);
        }

        curl_close($curl);
        return json_decode($output);
    }

    function get_binary($url, $data = [], $headers = [])
    {
        $curl = curl_init();

        // Build query string
        $params = $this->base_url . $url;
        if (!empty($data)) {
            $params .= '?' . http_build_query($data);
        }

        // Default headers (NO JSON assumptions)
        $default_headers = [
            'Accept: application/pdf',
        ];

        if (!empty($this->token)) {
            $default_headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $final_headers = !empty($headers)
            ? array_merge($default_headers, $headers)
            : $default_headers;

        curl_setopt_array($curl, [
            CURLOPT_URL            => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET        => true,
            CURLOPT_HTTPHEADER     => $final_headers,
            CURLOPT_BINARYTRANSFER => true,
        ]);

        $output = curl_exec($curl);

        if (curl_errno($curl)) {
            // log_message('error', 'cURL Error: ' . curl_error($curl));
        }

        curl_close($curl);

        return $output; // 🔑 raw bytes
    }

    function post($url, $fields, $headers = null, $username = '', $password = '')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url . $url);
        curl_setopt($ch, CURLOPT_POST, true);
        $params = http_build_query($fields);
        if ($this->app_debug) {
            echo $url . ' <br>';
            print_pre($fields);
            echo '<br>';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($username && $password) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // log_message('error', 'cURL Error: ' . $error_msg);
        }

        curl_close($ch);
        return json_decode($output);
    }

    function post_json($url, $fields, $headers = [], $username = '', $password = '')
    {
        $ch = curl_init();

        $json_data = json_encode($fields);

        $default_headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data),
            'Accept: application/json'
        ];

        // Auto-attach Bearer token if present
        if (!empty($this->token)) {
            $default_headers[] = 'Authorization: Bearer ' . $this->token;
        }

        // Merge custom headers if provided
        if (!empty($headers)) {
            $final_headers = array_merge($default_headers, $headers);
        } else {
            $final_headers = $default_headers;
        }

        curl_setopt($ch, CURLOPT_URL, $this->base_url . $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data); // Pass the raw JSON string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $final_headers);

        // Basic Auth support
        if ($username && $password) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }

        // Debugging (consistent with your previous style)
        // if (config_item('app_debug')) {
        if ($this->app_debug) {
            echo 'curl -X POST "' . $this->base_url . $url . '"';
            // echo ' -H "Content-Type: application/json" ';
            foreach ($final_headers as $h) {
                echo '  -H "' . $h . '" ';
            }
            echo " -d '" . $json_data . "'";
            echo endl();
        }

        $output = curl_exec($ch);

        // Error Handling
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // log_message('error', 'cURL JSON Error: ' . $error_msg);
            curl_close($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Return decoded object or null if response is empty
        return $output ? json_decode($output) : null;
    }
}

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
}

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

    function get_profile()
    {
        return $this->client->get('/resident/profile');
    }
}


if (!function_exists('print_pre')) {
    function print_pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

if (!function_exists('endl')) {
    function endl()
    {
        return "\n";
    }
}
