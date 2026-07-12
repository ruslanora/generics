<?php

declare(strict_types=1);

namespace Ruslan\Generics;

/**
 * A node in a LinkedList, holding a value and links to its neighbours.
 *
 * @template T
 */
final class LinkedListNode
{
    /** @var T */
    public $value;

    /** @var self<T>|null */
    private ?self $next = null;

    /** @var self<T>|null */
    private ?self $prev = null;

    /** @var LinkedList<T>|null */
    private ?LinkedList $list = null;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return LinkedList<T>|null
     */
    public function getList(): ?LinkedList
    {
        return $this->list;
    }

    /**
     * @return self<T>|null
     */
    public function getNext(): ?self
    {
        return $this->next;
    }

    /**
     * @return self<T>|null
     */
    public function getPrevious(): ?self
    {
        return $this->prev;
    }

    /**
     * @param LinkedList<T> $list
     *
     * @internal Called by LinkedList when this node joins it.
     */
    public function attachTo(LinkedList $list): void
    {
        $this->list = $list;
    }

    /**
     * @param self<T>|null $node
     *
     * @internal Called by LinkedList to relink neighbours.
     */
    public function setNext(?self $node): void
    {
        $this->next = $node;
    }

    /**
     * @param self<T>|null $node
     *
     * @internal Called by LinkedList to relink neighbours.
     */
    public function setPrevious(?self $node): void
    {
        $this->prev = $node;
    }

    /**
     * @internal Called by LinkedList once this node has been removed.
     */
    public function detach(): void
    {
        $this->list = null;
        $this->next = null;
        $this->prev = null;
    }
}
