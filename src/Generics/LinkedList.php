<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use Countable;
use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use UnderflowException;

/**
 * A doubly linked list. Unlike an array-backed list, inserting or removing next
 * to a node you already hold is O(1) - that's the whole reason to reach for this
 * over a plain array.
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class LinkedList implements Countable, IteratorAggregate
{
    /** @var LinkedListNode<T>|null */
    private ?LinkedListNode $head = null;

    /** @var LinkedListNode<T>|null */
    private ?LinkedListNode $tail = null;

    private int $count = 0;

    /**
     * @param iterable<T> $values
     */
    public function __construct(iterable $values = [])
    {
        foreach ($values as $value) {
            $this->addLast($value);
        }
    }

    /**
     * @return LinkedListNode<T>|null
     */
    public function getFirst(): ?LinkedListNode
    {
        return $this->head;
    }

    /**
     * @return LinkedListNode<T>|null
     */
    public function getLast(): ?LinkedListNode
    {
        return $this->tail;
    }

    public function count(): int
    {
        return max(0, $this->count);
    }

    /**
     * @param T $value
     *
     * @return LinkedListNode<T>
     */
    public function addFirst($value): LinkedListNode
    {
        $node = new LinkedListNode($value);
        $node->attachTo($this);

        if ($this->head === null) {
            $this->head = $node;
            $this->tail = $node;
        } else {
            $node->setNext($this->head);
            $this->head->setPrevious($node);
            $this->head = $node;
        }

        $this->count++;

        return $node;
    }

    /**
     * @param T $value
     *
     * @return LinkedListNode<T>
     */
    public function addLast($value): LinkedListNode
    {
        $node = new LinkedListNode($value);
        $node->attachTo($this);

        if ($this->tail === null) {
            $this->head = $node;
            $this->tail = $node;
        } else {
            $node->setPrevious($this->tail);
            $this->tail->setNext($node);
            $this->tail = $node;
        }

        $this->count++;

        return $node;
    }

    /**
     * @param LinkedListNode<T> $node
     * @param T $value
     *
     * @return LinkedListNode<T>
     */
    public function addBefore(LinkedListNode $node, $value): LinkedListNode
    {
        $this->assertOwnedByThis($node);

        $new = new LinkedListNode($value);
        $new->attachTo($this);

        $prev = $node->getPrevious();
        $new->setPrevious($prev);
        $new->setNext($node);
        $node->setPrevious($new);

        if ($prev === null) {
            $this->head = $new;
        } else {
            $prev->setNext($new);
        }

        $this->count++;

        return $new;
    }

    /**
     * @param LinkedListNode<T> $node
     * @param T $value
     *
     * @return LinkedListNode<T>
     */
    public function addAfter(LinkedListNode $node, $value): LinkedListNode
    {
        $this->assertOwnedByThis($node);

        $new = new LinkedListNode($value);
        $new->attachTo($this);

        $next = $node->getNext();
        $new->setNext($next);
        $new->setPrevious($node);
        $node->setNext($new);

        if ($next === null) {
            $this->tail = $new;
        } else {
            $next->setPrevious($new);
        }

        $this->count++;

        return $new;
    }

    /**
     * @param LinkedListNode<T> $node
     */
    public function removeNode(LinkedListNode $node): void
    {
        $this->assertOwnedByThis($node);

        $prev = $node->getPrevious();
        $next = $node->getNext();

        if ($prev === null) {
            $this->head = $next;
        } else {
            $prev->setNext($next);
        }

        if ($next === null) {
            $this->tail = $prev;
        } else {
            $next->setPrevious($prev);
        }

        $node->detach();
        $this->count--;
    }

    public function removeFirst(): void
    {
        if ($this->head === null) {
            throw new UnderflowException('Cannot remove from an empty linked list.');
        }

        $this->removeNode($this->head);
    }

    public function removeLast(): void
    {
        if ($this->tail === null) {
            throw new UnderflowException('Cannot remove from an empty linked list.');
        }

        $this->removeNode($this->tail);
    }

    /**
     * @param T $value
     */
    public function remove($value): bool
    {
        $node = $this->find($value);

        if ($node === null) {
            return false;
        }

        $this->removeNode($node);

        return true;
    }

    /**
     * @param T $value
     */
    public function contains($value): bool
    {
        return $this->find($value) !== null;
    }

    /**
     * @param T $value
     *
     * @return LinkedListNode<T>|null
     */
    public function find($value): ?LinkedListNode
    {
        for ($node = $this->head; $node !== null; $node = $node->getNext()) {
            if ($node->value === $value) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @param T $value
     *
     * @return LinkedListNode<T>|null
     */
    public function findLast($value): ?LinkedListNode
    {
        for ($node = $this->tail; $node !== null; $node = $node->getPrevious()) {
            if ($node->value === $value) {
                return $node;
            }
        }

        return null;
    }

    public function clear(): void
    {
        for ($node = $this->head; $node !== null;) {
            $next = $node->getNext();
            $node->detach();
            $node = $next;
        }

        $this->head = null;
        $this->tail = null;
        $this->count = 0;
    }

    /**
     * @return Generator<int, T>
     */
    public function getIterator(): Generator
    {
        for ($node = $this->head; $node !== null; $node = $node->getNext()) {
            yield $node->value;
        }
    }

    /**
     * @param LinkedListNode<T> $node
     */
    private function assertOwnedByThis(LinkedListNode $node): void
    {
        if ($node->getList() !== $this) {
            throw new InvalidArgumentException('The given node does not belong to this linked list.');
        }
    }
}
