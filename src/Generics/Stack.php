<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use Countable;
use Generator;
use IteratorAggregate;
use UnderflowException;

/**
 * A LIFO collection backed by a plain PHP array. Iteration runs top to bottom,
 * matching the enumeration order of .NET's Stack<T>, rather than insertion order.
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class Stack implements Countable, IteratorAggregate
{
    /** @var list<T> */
    private array $items = [];

    /**
     * @param iterable<T> $values
     */
    public function __construct(iterable $values = [])
    {
        foreach ($values as $value) {
            $this->push($value);
        }
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param T $value
     */
    public function push($value): void
    {
        $this->items[] = $value;
    }

    /**
     * @return T
     */
    public function pop()
    {
        if ($this->items === []) {
            throw new UnderflowException('Cannot pop from an empty stack.');
        }

        return array_pop($this->items);
    }

    /**
     * @return T
     */
    public function peek()
    {
        if ($this->items === []) {
            throw new UnderflowException('Cannot peek an empty stack.');
        }

        return $this->items[count($this->items) - 1];
    }

    /**
     * @param T $value
     *
     * @param-out T $value
     */
    public function tryPop(&$value): bool
    {
        if ($this->items === []) {
            return false;
        }

        $value = array_pop($this->items);

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

        $value = $this->items[count($this->items) - 1];

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
        for ($i = count($this->items) - 1; $i >= 0; $i--) {
            yield $this->items[$i];
        }
    }
}
