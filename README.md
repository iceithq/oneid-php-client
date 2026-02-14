# OneID PHP Client

PHP client library for integrating with the OneID API at `https://oneid.systems/api/v1`.

## Features

- Simple and intuitive API interface
- PSR-4 autoloading support
- Comprehensive error handling with custom exceptions
- Support for all HTTP methods (GET, POST, PUT, DELETE)
- Built-in authentication methods
- Configurable timeout and debug mode
- Easy integration with any PHP application

## Requirements

- PHP >= 7.4
- cURL extension
- JSON extension

## Installation

### Via Composer (Recommended)

If you want to use this package in your projects (oneid-delivery, oneid-portal, etc.), add this to your main project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../oneid-php-client"
    }
  ],
  "require": {
    "iceithq/oneid-php-client": "*"
  }
}
```

Then run:

```bash
composer install
```

### Manual Installation

Clone this repository and include the autoloader:

```php
require_once 'path/to/oneid-php-client/vendor/autoload.php';
```

## Quick Start

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use OneID\Client;
use OneID\Config;

// Create configuration
$config = new Config([
    'base_url' => 'https://oneid.systems/api/v1',
    'api_key' => 'your-api-key-here', // Optional, can be set later
    'timeout' => 30,
    'debug' => false
]);

// Initialize client
$client = new Client($config);

// Make API calls
try {
    $response = $client->get('/users');
    print_r($response);
} catch (OneID\Exceptions\OneIDException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### Authentication

```php
// Login
try {
    $result = $client->authenticate('username', 'password');
    $token = $result['token'];
    
    // Set token for future requests
    $client->setApiKey($token);
    
} catch (OneID\Exceptions\AuthenticationException $e) {
    echo 'Authentication failed: ' . $e->getMessage();
}
```

### Verify Token

```php
try {
    $result = $client->verifyToken($token);
    if ($result['valid']) {
        echo 'Token is valid';
    }
} catch (OneID\Exceptions\OneIDException $e) {
    echo 'Token verification failed: ' . $e->getMessage();
}
```

### Get User Profile

```php
// Get current authenticated user
$profile = $client->getProfile();

// Get specific user by ID
$profile = $client->getProfile('user-id-123');
```

### Refresh Token

```php
try {
    $result = $client->refreshToken($refreshToken);
    $newToken = $result['token'];
    $client->setApiKey($newToken);
} catch (OneID\Exceptions\OneIDException $e) {
    echo 'Token refresh failed: ' . $e->getMessage();
}
```

### Logout

```php
$client->logout();
```

## API Methods

### HTTP Methods

```php
// GET request
$response = $client->get('/endpoint', ['param1' => 'value1']);

// POST request
$response = $client->post('/endpoint', ['field1' => 'value1']);

// PUT request
$response = $client->put('/endpoint', ['field1' => 'value1']);

// DELETE request
$response = $client->delete('/endpoint', ['param1' => 'value1']);
```

### Authentication Methods

| Method | Description |
|--------|-------------|
| `authenticate($username, $password)` | Login with credentials |
| `verifyToken($token)` | Verify token validity |
| `refreshToken($refreshToken)` | Refresh authentication token |
| `getProfile($userId = null)` | Get user profile |
| `logout()` | Logout current user |
| `setApiKey($apiKey)` | Set API key for authenticated requests |

## Configuration Options

```php
$config = new Config([
    'base_url' => 'https://oneid.systems/api/v1',  // API base URL
    'api_key' => 'your-api-key',                   // API key/token
    'timeout' => 30,                                // Request timeout in seconds
    'debug' => false,                               // Enable debug mode
    'options' => [                                  // Custom options
        'custom_option' => 'value'
    ]
]);
```

### Configuration Methods

```php
// Getters
$config->getBaseUrl();
$config->getApiKey();
$config->getTimeout();
$config->isDebugMode();
$config->getOption('key', 'default');

// Setters (chainable)
$config->setBaseUrl('https://api.example.com')
       ->setApiKey('new-key')
       ->setTimeout(60)
       ->setDebugMode(true)
       ->setOption('key', 'value');
```

## Error Handling

The client uses custom exceptions for different error scenarios:

```php
use OneID\Exceptions\OneIDException;
use OneID\Exceptions\AuthenticationException;
use OneID\Exceptions\ValidationException;

try {
    $client->post('/users', $userData);
} catch (ValidationException $e) {
    // Handle validation errors (422)
    echo 'Validation failed: ' . $e->getMessage();
    print_r($e->getErrors());
} catch (AuthenticationException $e) {
    // Handle authentication errors (401, 403)
    echo 'Authentication failed: ' . $e->getMessage();
} catch (OneIDException $e) {
    // Handle other API errors
    echo 'API Error: ' . $e->getMessage();
    echo 'HTTP Code: ' . $e->getCode();
}
```

### Exception Types

| Exception | HTTP Codes | Description |
|-----------|------------|-------------|
| `AuthenticationException` | 401, 403 | Authentication/authorization failures |
| `ValidationException` | 422 | Validation errors |
| `OneIDException` | All others | General API errors |

## Integration Examples

### In oneid-delivery (Riders App)

```php
// public/index.php or bootstrap file
require_once __DIR__ . '/../vendor/autoload.php';

use OneID\Client;
use OneID\Config;

// Initialize OneID client
$oneIdConfig = new Config([
    'base_url' => 'https://oneid.systems/api/v1',
    'timeout' => 30
]);

$oneIdClient = new Client($oneIdConfig);

// Store in container or global scope
$GLOBALS['oneid'] = $oneIdClient;

// Use in your application
function authenticateRider($username, $password) {
    global $oneIdClient;
    
    try {
        $result = $oneIdClient->authenticate($username, $password);
        $_SESSION['rider_token'] = $result['token'];
        $_SESSION['rider_id'] = $result['user']['id'];
        return true;
    } catch (OneID\Exceptions\AuthenticationException $e) {
        return false;
    }
}
```

### In oneid-portal (Residents App)

```php
// config/oneid.php
return [
    'base_url' => getenv('ONEID_BASE_URL') ?: 'https://oneid.systems/api/v1',
    'api_key' => getenv('ONEID_API_KEY'),
    'timeout' => 30,
    'debug' => getenv('APP_ENV') === 'development'
];

// services/OneIDService.php
class OneIDService {
    private $client;
    
    public function __construct() {
        $config = require __DIR__ . '/../config/oneid.php';
        $this->client = new OneID\Client(new OneID\Config($config));
    }
    
    public function verifyResident($token) {
        return $this->client->verifyToken($token);
    }
    
    public function getResidentProfile($userId) {
        return $this->client->getProfile($userId);
    }
}
```

## Development

### Running Tests

```bash
composer test
```

### Directory Structure

```
oneid-php-client/
├── src/
│   ├── Client.php              # Main API client
│   ├── Config.php              # Configuration class
│   └── Exceptions/             # Exception classes
│       ├── OneIDException.php
│       ├── AuthenticationException.php
│       └── ValidationException.php
├── examples/                   # Usage examples
├── tests/                      # Unit tests
├── composer.json              # Composer configuration
└── README.md                  # This file
```

## License

MIT License

## Support

For issues and questions, please contact ICEIT HQ development team.

## Changelog

### Version 1.0.0 (2026-02-14)
- Initial release
- Basic API client functionality
- Authentication support
- Error handling with custom exceptions
- PSR-4 autoloading
