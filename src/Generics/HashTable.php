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
 * A map keyed by any type, built on separate chaining rather than delegating to a
 * native PHP array the way HashMap does: each bucket is itself a LinkedList, and
 * colliding keys are chained onto it instead of relying on PHP's own hash table.
 * The bucket array grows (doubles, rehashing every entry) once the load factor
 * would otherwise exceed 0.75, keeping chains short as the table grows.
 *
 * Because entries live in whichever bucket their hash lands in rather than in
 * insertion order, iteration order is unspecified and can change across a resize -
 * unlike HashMap, which inherits PHP's insertion-order iteration for free.
 *
 * @template TKey
 * @template TValue
 *
 * @implements IteratorAggregate<int, array{0: TKey, 1: TValue}>
 * @implements ArrayAccess<TKey, TValue>
 */
final class HashTable implements Countable, IteratorAggregate, ArrayAccess
{
    private const INITIAL_CAPACITY = 8;

    private const LOAD_FACTOR_THRESHOLD = 0.75;

    private int $capacity = self::INITIAL_CAPACITY;

    /** @var list<LinkedList<array{0: TKey, 1: TValue}>> */
    private array $buckets = [];

    private int $count = 0;

    /**
     * @param iterable<array{0: TKey, 1: TValue}> $entries
     */
    public function __construct(iterable $entries = [])
    {
        $this->initializeBuckets($this->capacity);

        foreach ($entries as [$key, $value]) {
            $this->set($key, $value);
        }
    }

    public function count(): int
    {
        return max(0, $this->count);
    }

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function set($key, $value): void
    {
        $identity = $this->identityFor($key);
        $index = $this->indexFor($identity);
        $node = $this->findNode($this->buckets[$index], $identity);

        if ($node !== null) {
            $node->value = [$key, $value];

            return;
        }

        $this->buckets[$index]->addLast([$key, $value]);
        $this->count++;

        if ($this->count > (int) ($this->capacity * self::LOAD_FACTOR_THRESHOLD)) {
            $this->resize($this->capacity * 2);
        }
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
        if (!$this->tryGet($key, $value)) {
            throw new OutOfBoundsException('No entry found for the given key.');
        }

        return $value;
    }

    /**
     * @param TKey $key
     * @param TValue $value
     *
     * @param-out TValue $value
     */
    public function tryGet($key, &$value): bool
    {
        $identity = $this->identityFor($key);
        $node = $this->findNode($this->buckets[$this->indexFor($identity)], $identity);

        if ($node === null) {
            return false;
        }

        $value = $node->value[1];

        return true;
    }

    /**
     * @param TKey $key
     */
    public function has($key): bool
    {
        $identity = $this->identityFor($key);

        return $this->findNode($this->buckets[$this->indexFor($identity)], $identity) !== null;
    }

    /**
     * @param TKey $key
     */
    public function remove($key): bool
    {
        $identity = $this->identityFor($key);
        $index = $this->indexFor($identity);
        $node = $this->findNode($this->buckets[$index], $identity);

        if ($node === null) {
            return false;
        }

        $this->buckets[$index]->removeNode($node);
        $this->count--;

        return true;
    }

    public function clear(): void
    {
        $this->capacity = self::INITIAL_CAPACITY;
        $this->initializeBuckets($this->capacity);
        $this->count = 0;
    }

    /**
     * @return Generator<int, TKey>
     */
    public function keys(): Generator
    {
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as [$key]) {
                yield $key;
            }
        }
    }

    /**
     * @return Generator<int, TValue>
     */
    public function values(): Generator
    {
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as [, $value]) {
                yield $value;
            }
        }
    }

    /**
     * @return Generator<int, array{0: TKey, 1: TValue}>
     */
    public function getIterator(): Generator
    {
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                yield $entry;
            }
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
                'HashTable entries must be set with an explicit key; $table[] = $value is not supported.',
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
     * @param LinkedList<array{0: TKey, 1: TValue}> $bucket
     *
     * @return LinkedListNode<array{0: TKey, 1: TValue}>|null
     */
    private function findNode(LinkedList $bucket, int|string $identity): ?LinkedListNode
    {
        for ($node = $bucket->getFirst(); $node !== null; $node = $node->getNext()) {
            if ($this->identityFor($node->value[0]) === $identity) {
                return $node;
            }
        }

        return null;
    }

    private function indexFor(int|string $identity): int
    {
        if (is_int($identity)) {
            return (($identity % $this->capacity) + $this->capacity) % $this->capacity;
        }

        return crc32($identity) % $this->capacity;
    }

    private function resize(int $newCapacity): void
    {
        $oldBuckets = $this->buckets;
        $this->capacity = $newCapacity;
        $this->initializeBuckets($newCapacity);

        foreach ($oldBuckets as $bucket) {
            foreach ($bucket as $entry) {
                $index = $this->indexFor($this->identityFor($entry[0]));
                $this->buckets[$index]->addLast($entry);
            }
        }
    }

    private function initializeBuckets(int $capacity): void
    {
        $this->buckets = [];

        for ($i = 0; $i < $capacity; $i++) {
            $this->buckets[] = new LinkedList();
        }
    }

    /**
     * @param TKey $key
     */
    private function identityFor($key): int|string
    {
        return match (true) {
            $key === null => throw new InvalidArgumentException('HashTable keys cannot be null.'),
            is_int($key) => $key,
            is_string($key) => 's:' . $key,
            is_bool($key) => 'b:' . ($key ? '1' : '0'),
            is_float($key) => 'f:' . (is_nan($key) ? 'NAN' : (string) $key),
            is_object($key) => 'o:' . spl_object_id($key),
            default => throw new InvalidArgumentException('Unsupported key type: ' . get_debug_type($key)),
        };
    }
}
