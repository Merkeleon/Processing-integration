# Crypto processing integration for PHP

## Installation
First, require the package using Composer:

`composer require merkeleon/processing-integration`

## Examples

### Take address

```php
use Merkeleon\Processing\API;
use Merkeleon\Processing\Address;

$api = API::make('URL', 'API_KEY', 'API_SECRET');

/** @var Address $address */
$address = $api->addressTake('BTC', 'identity');
```


### Make withdraw

```php
use Merkeleon\Processing\API;
use Merkeleon\Processing\Address;

$api = API::make('URL', 'API_KEY', 'API_SECRET');

/** @var Address $address */
$address = $api->withdraw('12c6DSiU4Rq3P4ZxziKxzrL5LmMBrzjrJX', 1, 'BTC', 'unique-identity-for-withdrawal', null);
```

### Description

Never use the timestamp as $foreignId for withdrawals, but use your unique identifier. If you mistakenly make two requests for output with the same $foreignId, then second and next attempts will be rejected for security reasons.
Foreign id for address may be not unique