# Generics Collections in PHP

[![CI](https://github.com/ruslanora/generics/actions/workflows/ci.yml/badge.svg)](https://github.com/ruslanora/generics/actions/workflows/ci.yml)
[![Packagist Version](https://img.shields.io/packagist/v/ruslanora/generics)](https://packagist.org/packages/ruslanora/generics)
[![PHP Version](https://img.shields.io/packagist/php-v/ruslanora/generics)](https://packagist.org/packages/ruslanora/generics)
[![License](https://img.shields.io/github/license/ruslanora/generics)](LICENSE)

Type-safe collections for PHP, powered by PHPStan generics, inspired by the [C# Generics Collection](https://learn.microsoft.com/en-us/dotnet/api/system.collections.generic) library.

It implements the following classes:

- `Stack` — a last-in-first-out (LIFO) collection.
- `Queue` — a first-in-first-out (FIFO) collection.
- `PriorityQueue` — a queue where elements are dequeued in priority order.
- `LinkedList` — a doubly linked list.
- `HashMap` — a key/value collection.
- `HashTable` — a collection of key/value pairs organized by the hash code of the key.
- `Comparer` — a base class for defining custom comparison logic used by the collections above.

## Installation

```sh
composer require ruslanora/generics
```

## Usage

```php
use Ruslan\Generics\LinkedList;

$list = new LinkedList(['b', 'c']);

$first = $list->addFirst('a'); // a, b, c
$list->addAfter($first, 'a2'); // a, a2, b, c

foreach ($list as $value) {
    // a, a2, b, c
}
```

For the rest of the classes, check the [wiki](https://github.com/ruslanora/generics/wiki).

## Contributing

Thank you for considering contributing to the project! The contribution guide can be found in [this guide](CONTRIBUTING.md).

## Code of Conduct

In order to ensure that everybody is welcome, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review the [Security Policy](SECURITY.md) on how to report security vulnerabilities.

## License

This package is open-sourced software licensed under the [MIT License](LICENSE).
