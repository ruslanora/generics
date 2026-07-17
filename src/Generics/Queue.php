<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use Countable;
use Generator;
use IteratorAggregate;
use UnderflowException;

/**
 * A FIFO collection backed by a plain PHP array. Iteration runs front to back,
 * matching the enumeration order of .NET's Queue<T>, i.e. dequeue order.
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class Queue implements Countable, IteratorAggregate
{
    /** @var list<T> */
    private array $items = [];

    /**
     * @param iterable<T> $values
     */
    public function __construct(iterable $values = [])
    {
        foreach ($values as $value) {
            $this->enqueue($value);
        }
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param T $value
     */
    public function enqueue($value): void
    {
        $this->items[] = $value;
    }

    /**
     * @return T
     */
    public function dequeue()
    {
        if ($this->items === []) {
            throw new UnderflowException('Cannot dequeue from an empty queue.');
        }

        return array_shift($this->items);
    }

    /**
     * @return T
     */
    public function peek()
    {
        if ($this->items === []) {
            throw new UnderflowException('Cannot peek an empty queue.');
        }

        return $this->items[0];
    }

    /**
     * @param T $value
     *
     * @param-out T $value
     */
    public function tryDequeue(&$value): bool
    {
        if ($this->items === []) {
            return false;
        }

        $value = array_shift($this->items);

        return true;
    }

    /**
     * @param T $value
     *
     * @param-out T $value
     */
    public function tryPeek(&$value): bool
    {
        if ($this->items === []) {
            return false;
        }

        $value = $this->items[0];

        return true;
    }

    /**
     * @param T $value
     */
    public function contains($value): bool
    {
        foreach ($this->items as $item) {
            if ($item === $value) {
                return true;
            }
        }

        return false;
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * @return Generator<int, T>
     */
    public function getIterator(): Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }
}
