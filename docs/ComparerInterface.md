# ComparerInterface

Defines a method that a type implements to compare two values. Implemented by [`Comparer`](Comparer.md); implement it directly if you want a reusable comparer class rather than a one-off closure passed to `Comparer::create()`.

```php
use Ruslan\Generics\Interfaces\ComparerInterface;

/**
 * @implements ComparerInterface<int>
 */
final class ReverseIntComparer implements ComparerInterface
{
    public function compare($x, $y): int
    {
        return $y <=> $x;
    }
}
```

## Methods

### `compare(mixed $x, mixed $y): int`

Returns a negative number if `$x` precedes `$y`, zero if they are equal, a positive number if `$x` follows `$y`.

## See also

- [`Comparer`](Comparer.md)
