# Quick Start Guide

## Installation in Your Project

### For oneid-delivery or oneid-portal

1. Navigate to your project directory:
```bash
cd c:\xampp\htdocs\iceithq\oneid-delivery
# or
cd c:\xampp\htdocs\iceithq\oneid-portal
```

2. Add to your `composer.json`:
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

3. Install the package:
```bash
composer install
```

## Basic Implementation

### 1. Initialize the Client

Create a file `config/oneid.php`:
```php
<?php
return [
    'base_url' => 'https://oneid.systems/api/v1',
    'timeout' => 30,
    'debug' => false
];
```

### 2. Create a Service Class

Create `services/OneIDService.php`:
```php
<?php
namespace App\Services;

use OneID\Client;
use OneID\Config;

class OneIDService {
    private $client;
    
    public function __construct() {
        $config = require __DIR__ . '/../config/oneid.php';
        $this->client = new Client(new Config($config));
    }
    
    public function login($username, $password) {
        return $this->client->authenticate($username, $password);
    }
    
    public function verifyToken($token) {
        return $this->client->verifyToken($token);
    }
    
    public function getProfile($token) {
        $this->client->setApiKey($token);
        return $this->client->getProfile();
    }
}
```

### 3. Use in Your Application

```php
<?php
require_once 'vendor/autoload.php';

use App\Services\OneIDService;

$oneId = new OneIDService();

// Login
try {
    $result = $oneId->login($_POST['username'], $_POST['password']);
    $_SESSION['token'] = $result['token'];
    $_SESSION['user'] = $result['user'];
    header('Location: /dashboard');
} catch (Exception $e) {
    $error = 'Login failed: ' . $e->getMessage();
}
```

## Common Use Cases

### For Riders (oneid-delivery)

```php
// Authenticate rider
$result = $oneId->login($riderEmail, $password);
$_SESSION['rider_token'] = $result['token'];
$_SESSION['rider_id'] = $result['user']['id'];

// Verify rider on each request
try {
    $verification = $oneId->verifyToken($_SESSION['rider_token']);
    if (!$verification['valid']) {
        // Redirect to login
    }
} catch (Exception $e) {
    // Handle error
}
```

### For Residents (oneid-portal)

```php
// Authenticate resident
$result = $oneId->login($residentEmail, $password);
$_SESSION['resident_token'] = $result['token'];

// Get resident profile
$profile = $oneId->getProfile($_SESSION['resident_token']);
echo "Welcome, " . $profile['name'];
```

## Next Steps

1. Review the full [README.md](README.md) for detailed API documentation
2. Check [examples/usage.php](examples/usage.php) for more examples
3. Implement authentication in your application
4. Add error handling for production use
5. Set up environment variables for configuration

## Support

For questions or issues, contact the ICEIT HQ development team.
