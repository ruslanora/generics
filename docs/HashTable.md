# HashTable

A map keyed by any type, built on separate chaining rather than delegating to a native PHP array the way [`HashMap`](HashMap.md) does: each bucket is itself a [`LinkedList`](LinkedList.md), and colliding keys are chained onto it instead of relying on PHP's own hash table. The bucket array grows (doubles, rehashing every entry) once the load factor would otherwise exceed 0.75, keeping chains short as the table grows.

Because entries live in whichever bucket their hash lands in rather than in insertion order, iteration order is unspecified and can change across a resize — unlike `HashMap`, which inherits PHP's insertion-order iteration for free. Reach for `HashTable` when you want to see the mechanics of a textbook hash table; reach for `HashMap` otherwise.

```php
use Ruslan\Generics\HashTable;

/** @var HashTable<string, int> $table */
$table = new HashTable();

$table->set('a', 1);
$table->set('b', 2);

$table->get('a');   // 1
$table->has('b');   // true
$table['a'];         // 1 (ArrayAccess is supported too)

foreach ($table as [$key, $value]) {
    // order is unspecified
}
```

Supported key types: `int`, `string`, `bool`, `float`, and objects (compared by identity, not by value). `null` keys are not supported.

## Constructor

### `__construct(iterable $entries = [])`

Creates a table. `$entries` is an iterable of `[$key, $value]` tuples, each set in order via `set()`.

## Methods

### `count(): int`

Number of entries currently in the table.

### `set(mixed $key, mixed $value): void`

Sets `$key` to `$value`, overwriting any existing entry for that key. May trigger a resize.

### `add(mixed $key, mixed $value): void`

Sets `$key` to `$value`.

Throws `InvalidArgumentException` if an entry for `$key` already exists.

### `get(mixed $key): mixed`

Returns the value for `$key`.

Throws `OutOfBoundsException` if no entry exists for `$key`.

### `tryGet(mixed $key, mixed &$value): bool`

Attempts to read the value for `$key` into `$value`. Returns `true` on success, `false` if no entry exists, without throwing.

### `has(mixed $key): bool`

Returns `true` if an entry exists for `$key`.

### `remove(mixed $key): bool`

Removes the entry for `$key`. Returns `true` if an entry was removed, `false` if none existed.

### `clear(): void`

Removes all entries and resets capacity back to its initial size.

### `keys(): Generator`

Yields each key in the table, in unspecified order.

### `values(): Generator`

Yields each value in the table, in unspecified order.

## Iteration

`HashTable` implements `IteratorAggregate`, `Countable`, and `ArrayAccess`.

Iterating yields `[$key, $value]` tuples, in unspecified (bucket) order.

### `ArrayAccess`

`isset($table[$key])`, `$table[$key]`, `$table[$key] = $value`, and `unset($table[$key])` are all supported and delegate to `has()`, `get()`, `set()`, and `remove()` respectively. `$table[] = $value` (appending without a key) is not supported and throws `InvalidArgumentException`.
