# HashMap

A map keyed by any type, including objects — something a native PHP array can't do, since its keys are limited to `int` and `string`. Internally it delegates to a plain PHP array, so lookups, inserts, and removals are as fast as PHP's own array — the tradeoff is that it doesn't control collision handling itself (see [`HashTable`](HashTable.md) if you need that).

```php
use Ruslan\Generics\HashMap;

/** @var HashMap<object, string> $map */
$map = new HashMap();

$key = new stdClass();
$map->set($key, 'value');

$map->get($key);   // 'value'
$map->has($key);   // true
$map[$key];         // 'value' (ArrayAccess is supported too)

foreach ($map as [$k, $v]) {
    // [$key, 'value']
}
```

Supported key types: `int`, `string`, `bool`, `float`, and objects (compared by identity, not by value). `null` keys are not supported.

## Constructor

### `__construct(iterable $entries = [])`

Creates a map. `$entries` is an iterable of `[$key, $value]` tuples, each set in order via `set()`.

## Methods

### `count(): int`

Number of entries currently in the map.

### `set(mixed $key, mixed $value): void`

Sets `$key` to `$value`, overwriting any existing entry for that key.

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

Removes all entries.

### `keys(): Generator`

Yields each key in the map, in insertion order.

### `values(): Generator`

Yields each value in the map, in insertion order.

## Iteration

`HashMap` implements `IteratorAggregate`, `Countable`, and `ArrayAccess`.

Iterating yields `[$key, $value]` tuples rather than `$key => $value` pairs on purpose: a native PHP iteration key can only ever be an `int` or `string`, so yielding real key/value pairs would break the moment the key is an object.

### `ArrayAccess`

`isset($map[$key])`, `$map[$key]`, `$map[$key] = $value`, and `unset($map[$key])` are all supported and delegate to `has()`, `get()`, `set()`, and `remove()` respectively. `$map[] = $value` (appending without a key) is not supported and throws `InvalidArgumentException`.
