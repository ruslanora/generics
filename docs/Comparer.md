# Comparer

Base class for a generic comparer, implementing [`ComparerInterface`](ComparerInterface.md). Used by [`PriorityQueue`](PriorityQueue.md) to order priorities, and useful anywhere else you need to plug custom ordering logic into a collection.

You won't normally extend `Comparer` directly — use one of its two factory methods instead.

```php
use Ruslan\Generics\Comparer;

// Natural ordering via <=>, for scalars only.
$natural = Comparer::default();
$natural->compare(1, 2); // -1

// Custom ordering via a closure, for anything else (objects, arrays, reverse order, ...).
$byLength = Comparer::create(
    fn (string $a, string $b): int => strlen($a) <=> strlen($b),
);
$byLength->compare('a', 'bb'); // -1
```

## Static methods

### `default(): self`

Returns a comparer that compares two scalar values with the spaceship operator (`<=>`).

Throws `InvalidArgumentException` if either value is not a scalar (objects, arrays, etc. have no meaningful natural order) — use `create()` instead for those.

### `create(Closure $comparison): self`

Returns a comparer backed by `$comparison`, a `Closure(T, T): int` that follows the same contract as `compare()`.

## Instance methods

### `compare(mixed $x, mixed $y): int`

Compares `$x` and `$y`. Returns a negative number if `$x` precedes `$y`, zero if they are equal, a positive number if `$x` follows `$y`.

## See also

- [`ComparerInterface`](ComparerInterface.md)
- [`PriorityQueue`](PriorityQueue.md)
