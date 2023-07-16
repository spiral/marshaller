# Marshaller

[![PHP Version Require](https://poser.pugx.org/spiral/marshaller/require/php)](https://packagist.org/packages/spiral/marshaller)
[![Latest Stable Version](https://poser.pugx.org/spiral/marshaller/v/stable)](https://packagist.org/packages/spiral/marshaller)
[![phpunit](https://github.com/spiral/marshaller/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral/marshaller/actions)
[![psalm](https://github.com/spiral/marshaller/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral/marshaller/actions)
[![Codecov](https://codecov.io/gh/spiral/marshaller/branch/1.x/graph/badge.svg)](https://codecov.io/gh/spiral/marshaller)
[![Total Downloads](https://poser.pugx.org/spiral/marshaller/downloads)](https://packagist.org/packages/spiral/marshaller)
[![type-coverage](https://shepherd.dev/github/spiral/marshaller/coverage.svg)](https://shepherd.dev/github/spiral/marshaller)
[![psalm-level](https://shepherd.dev/github/spiral/marshaller/level.svg)](https://shepherd.dev/github/spiral/marshaller)

## Introduction

The Marshaller package is a PHP tool that helps you convert PHP objects into simple arrays and vice versa.
It allows you to marshal objects into array representations for serialization, storage, and transportation,
and unmarshal arrays back into objects.

This package provides easy-to-use methods for converting objects to arrays and restoring objects from arrays.
It supports handling nested objects and complex data structures.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+

## Installation

You can install the package via composer:

```bash
composer require spiral/marshaller
```

## Usage

> **Note**
> Here, the low-level usage of the package will be covered.
> [Marshaller Bridge](https://github.com/spiral/marshaller-bridge) is a ready-made integration for the Spiral Framework.

### Converting objects to arrays

For example, we have a User class that has simple string properties, an Address property, a Status enum, a UUID,
and an array of Address objects, and we need to serialize it:

#### User

```php
namespace App\Entity;

use Ramsey\Uuid\UuidInterface;
use Spiral\Marshaller\Meta\Marshal;
use Spiral\Marshaller\Meta\MarshalArray;
use Spiral\Marshaller\Type\EnumType;

class User
{
    public function __construct(
        #[Marshal]
        private UuidInterface $uuid,

        #[Marshal(name: 'first_name')]
        private string $firstName,

        #[Marshal(name: 'last_name')]
        private string $lastName,

        #[Marshal(of: Status::class, type: EnumType::class)]
        private Status $status,

        #[Marshal(of: Address::class)]
        private Address $address,

        #[Marshal(name: 'registered_at', of: \DateTimeImmutable::class)]
        private \DateTimeImmutable $registeredAt,

        #[MarshalArray(name: 'delivery_addresses', of: Address::class)]
        private array $deliveryAddresses
    ) {
    }
}
```

Using attributes, we specify which private and protected properties to serialize and configure additional Marshaller
parameters (for example, what type of array elements should be).

> **Note**
> Specifying the `Marshal` attribute is optional for public properties unless the property requires
> additional Marshaller configuration.

#### Address

```php
namespace App\Entity;

use Spiral\Marshaller\Meta\Marshal;

final class Address
{
    public function __construct(
        #[Marshal]
        private string $street,

        #[Marshal]
        private string $city,

        #[Marshal]
        private string $country
    ) {
    }
}
```

#### Status

```php
namespace App\Entity;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
```

To serialize a User object, we need to create a `Marshaller` object and call the **marshal** method
and pass the User object into it.

```php
use App\Entity\Address;
use App\Entity\Status;
use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Spiral\Attributes\AttributeReader;
use Spiral\Marshaller\Mapper\AttributeMapperFactory;
use Spiral\Marshaller\Marshaller;

$address = new Address(
    street: 'Washington St.',
    city: 'San Francisco',
    country: 'USA',
);

$deliveryAddresses = [
    new Address(
        street: 'Street 1',
        city: 'New York',
        country: 'USA',
    ),
    new Address(
        street: 'Street 2',
        city: 'Chicago',
        country: 'USA',
    )
];

$user = new User(
    uuid: Uuid::uuid4(),
    firstName: 'John',
    lastName: 'Doe',
    status: Status::Active,
    address: $address,
    registeredAt: new \DateTimeImmutable(),
    deliveryAddresses: $deliveryAddresses,
);

$marshaller = new Marshaller(new AttributeMapperFactory(new AttributeReader()));
$data = $marshaller->marshal($user);

// result:
array(7) {
  'uuid' =>
  string(36) "e9aa20f8-8425-4020-af63-4a88725286c4"
  'first_name' =>
  string(4) "John"
  'last_name' =>
  string(3) "Doe"
  'status' =>
  array(2) {
    'name' =>
    string(6) "Active"
    'value' =>
    string(6) "active"
  }
  'address' =>
  array(3) {
    'street' =>
    string(14) "Washington St."
    'city' =>
    string(13) "San Francisco"
    'country' =>
    string(3) "USA"
  }
  'registered_at' =>
  string(25) "2023-07-16T13:57:35+00:00"
  'delivery_addresses' =>
  array(2) {
    [0] =>
    array(3) {
      'street' =>
      string(8) "Street 1"
      'city' =>
      string(8) "New York"
      'country' =>
      string(3) "USA"
    }
    [1] =>
    array(3) {
      'street' =>
      string(8) "Street 2"
      'city' =>
      string(7) "Chicago"
      'country' =>
      string(3) "USA"
    }
  }
}
```

### Converting arrays to objects

To unserialize an array, we need to create a `Marshaller` object and call the **unmarshal** method
and pass the array with data and object to populate into it.

```php
use Spiral\Attributes\AttributeReader;
use Spiral\Marshaller\Mapper\AttributeMapperFactory;
use Spiral\Marshaller\Marshaller;

$marshaller = new Marshaller(new AttributeMapperFactory(new AttributeReader()));

$data = [
    'uuid' => '4730d422-19ec-4da8-a3be-d42a774e0f2f',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'status' => [
        'name' => 'Active',
        'value' => 'active'
    ],
    'address' => [
        'street' => 'Washington St.',
        'city' => 'San Francisco',
        'country' => 'USA',
    ],
    'registered_at' => '2023-07-16T13:23:04+00:00',
    'delivery_addresses' => [
        [
            'street' => 'Street 1',
            'city' => 'New York',
            'country' => 'USA',
        ],
        [
            'street' => 'Street 2',
            'city' => 'Chicago',
            'country' => 'USA',
        ]
    ],
];

$user = $marshaller->unmarshal(
    $data,
    (new \ReflectionClass(User::class))->newInstanceWithoutConstructor()
);

// result:
class App\Entity\User#453 (7) {
  private Ramsey\Uuid\UuidInterface $uuid =>
  class Ramsey\Uuid\Lazy\LazyUuidFromString#421 (2) {
    private ?Ramsey\Uuid\UuidInterface $unwrapped =>
    NULL
    private string $uuid =>
    string(36) "4730d422-19ec-4da8-a3be-d42a774e0f2f"
  }
  private string $firstName =>
  string(4) "John"
  private string $lastName =>
  string(3) "Doe"
  private App\Entity\Status $status =>
  enum App\Entity\Status::Active : string("active");
  private App\Entity\Address $address =>
  class App\Entity\Address#447 (3) {
    private string $street =>
    string(14) "Washington St."
    private string $city =>
    string(13) "San Francisco"
    private string $country =>
    string(3) "USA"
  }
  private DateTimeImmutable $registeredAt =>
  class DateTimeImmutable#412 (3) {
    public $date =>
    string(26) "2023-07-16 13:23:04.000000"
    public $timezone_type =>
    int(1)
    public $timezone =>
    string(6) "+00:00"
  }
  private array $deliveryAddresses =>
  array(2) {
    [0] =>
    class App\Entity\Address#392 (3) {
      private string $street =>
      string(8) "Street 1"
      private string $city =>
      string(8) "New York"
      private string $country =>
      string(3) "USA"
    }
    [1] =>
    class App\Entity\Address#443 (3) {
      private string $street =>
      string(8) "Street 2"
      private string $city =>
      string(7) "Chicago"
      private string $country =>
      string(3) "USA"
    }
  }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
