<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use ArrayAccess;
use Countable;
use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;

/**
 * A map keyed by any type, including objects - something a native PHP array can't do,
 * since its keys are limited to int and string.
 *
 * Iterating yields [key, value] tuples rather than key => value pairs on purpose:
 * a native PHP iteration key can only ever be an int or string, so yielding real
 * TKey => TValue pairs would break the moment TKey is an object.
 *
 * @template TKey
 * @template TValue
 *
 * @implements IteratorAggregate<int, array{0: TKey, 1: TValue}>
 * @implements ArrayAccess<TKey, TValue>
 */
final class HashMap implements Countable, IteratorAggregate, ArrayAccess
{
    /** @var array<int|string, array{0: TKey, 1: TValue}> */
    private array $entries = [];

    /**
     * @param iterable<array{0: TKey, 1: TValue}> $entries
     */
    public function __construct(iterable $entries = [])
    {
        foreach ($entries as [$key, $value]) {
            $this->set($key, $value);
        }
    }

    public function count(): int
    {
        return count($this->entries);
    }

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function set($key, $value): void
    {
        $this->entries[$this->bucketKeyFor($key)] = [$key, $value];
    }

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function add($key, $value): void
    {
        if ($this->has($key)) {
            throw new InvalidArgumentException('An entry with the given key already exists.');
        }

        $this->set($key, $value);
    }

    /**
     * @param TKey $key
     *
     * @return TValue
     */
    public function get($key)
    {
        $bucketKey = $this->bucketKeyFor($key);

        if (!array_key_exists($bucketKey, $this->entries)) {
            throw new OutOfBoundsException('No entry found for the given key.');
        }

        return $this->entries[$bucketKey][1];
    }

    /**
     * @param TKey $key
     * @param TValue $value
     *
     * @param-out TValue $value
     */
    public function tryGet($key, &$value): bool
    {
        $bucketKey = $this->bucketKeyFor($key);

        if (!array_key_exists($bucketKey, $this->entries)) {
            return false;
        }

        $value = $this->entries[$bucketKey][1];

        return true;
    }

    /**
     * @param TKey $key
     */
    public function has($key): bool
    {
        return array_key_exists($this->bucketKeyFor($key), $this->entries);
    }

    /**
     * @param TKey $key
     */
    public function remove($key): bool
    {
        $bucketKey = $this->bucketKeyFor($key);

        if (!array_key_exists($bucketKey, $this->entries)) {
            return false;
        }

        unset($this->entries[$bucketKey]);

        return true;
    }

    public function clear(): void
    {
        $this->entries = [];
    }

    /**
     * @return Generator<int, TKey>
     */
    public function keys(): Generator
    {
        foreach ($this->entries as [$key]) {
            yield $key;
        }
    }

    /**
     * @return Generator<int, TValue>
     */
    public function values(): Generator
    {
        foreach ($this->entries as [, $value]) {
            yield $value;
        }
    }

    /**
     * @return Generator<int, array{0: TKey, 1: TValue}>
     */
    public function getIterator(): Generator
    {
        foreach ($this->entries as $entry) {
            yield $entry;
        }
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @param TKey $offset
     *
     * @return TValue
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param TKey|null $offset
     * @param TValue $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            throw new InvalidArgumentException(
                'HashMap entries must be set with an explicit key; $map[] = $value is not supported.',
            );
        }

        $this->set($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @param TKey $key
     */
    private function bucketKeyFor($key): int|string
    {
        return match (true) {
            $key === null => throw new InvalidArgumentException('HashMap keys cannot be null.'),
            is_int($key) => $key,
            is_string($key) => 's:' . $key,
            is_bool($key) => 'b:' . ($key ? '1' : '0'),
            is_float($key) => 'f:' . (is_nan($key) ? 'NAN' : (string) $key),
            is_object($key) => 'o:' . spl_object_id($key),
            default => throw new InvalidArgumentException('Unsupported key type: ' . get_debug_type($key)),
        };
    }
}
