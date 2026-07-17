<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use Countable;
use Generator;
use IteratorAggregate;
use Ruslan\Generics\Interfaces\ComparerInterface;
use UnderflowException;

/**
 * A binary min-heap keyed by priority: dequeue always returns the element with the
 * lowest priority first, per the given comparer (Comparer::default() otherwise).
 *
 * Iteration walks the heap's internal array layout rather than dequeue order - the
 * same tradeoff .NET's PriorityQueue<TElement,TPriority> makes with UnorderedItems -
 * because producing sorted output would mean either draining the heap or sorting a
 * copy on every iteration, and nothing about a heap promises iteration order anyway.
 *
 * @template TElement
 * @template TPriority
 *
 * @implements IteratorAggregate<int, array{0: TElement, 1: TPriority}>
 */
final class PriorityQueue implements Countable, IteratorAggregate
{
    /** @var list<array{0: TElement, 1: TPriority}> */
    private array $heap = [];

    /** @var ComparerInterface<TPriority> */
    private readonly ComparerInterface $priorityComparer;

    /**
     * @param iterable<array{0: TElement, 1: TPriority}> $items
     * @param ComparerInterface<TPriority>|null $priorityComparer
     */
    public function __construct(iterable $items = [], ?ComparerInterface $priorityComparer = null)
    {
        $this->priorityComparer = $priorityComparer ?? Comparer::default();

        foreach ($items as [$element, $priority]) {
            $this->enqueue($element, $priority);
        }
    }

    public function count(): int
    {
        return count($this->heap);
    }

    /**
     * @param TElement $element
     * @param TPriority $priority
     */
    public function enqueue($element, $priority): void
    {
        $this->heap[] = [$element, $priority];
        $this->siftUp(count($this->heap) - 1);
    }

    /**
     * @return TElement
     */
    public function dequeue()
    {
        if ($this->heap === []) {
            throw new UnderflowException('Cannot dequeue from an empty priority queue.');
        }

        return $this->removeRoot()[0];
    }

    /**
     * @return TElement
     */
    public function peek()
    {
        if ($this->heap === []) {
            throw new UnderflowException('Cannot peek an empty priority queue.');
        }

        return $this->heap[0][0];
    }

    /**
     * @param TElement $element
     * @param TPriority $priority
     *
     * @param-out TElement $element
     * @param-out TPriority $priority
     */
    public function tryDequeue(&$element, &$priority): bool
    {
        if ($this->heap === []) {
            return false;
        }

        [$element, $priority] = $this->removeRoot();

        return true;
    }

    /**
     * @param TElement $element
     * @param TPriority $priority
     *
     * @param-out TElement $element
     * @param-out TPriority $priority
     */
    public function tryPeek(&$element, &$priority): bool
    {
        if ($this->heap === []) {
            return false;
        }

        [$element, $priority] = $this->heap[0];

        return true;
    }

    /**
     * Equivalent to enqueue() followed by dequeue(), without the redundant sift when
     * the new element would just be dequeued straight back out.
     *
     * @param TElement $element
     * @param TPriority $priority
     *
     * @return TElement
     */
    public function enqueueDequeue($element, $priority)
    {
        if ($this->heap === [] || $this->priorityComparer->compare($priority, $this->heap[0][1]) <= 0) {
            return $element;
        }

        $root = $this->heap[0];
        $this->heap[0] = [$element, $priority];
        $this->siftDown(0);

        return $root[0];
    }

    public function clear(): void
    {
        $this->heap = [];
    }

    /**
     * @return Generator<int, array{0: TElement, 1: TPriority}>
     */
    public function getIterator(): Generator
    {
        foreach ($this->heap as $item) {
            yield $item;
        }
    }

    /**
     * @return array{0: TElement, 1: TPriority}
     */
    private function removeRoot(): array
    {
        $root = $this->heap[0];
        $lastIndex = count($this->heap) - 1;
        $last = $this->heap[$lastIndex];
        array_splice($this->heap, $lastIndex, 1);

        if ($this->heap !== []) {
            $this->heap[0] = $last;
            $this->siftDown(0);
        }

        return $root;
    }

    private function siftUp(int $index): void
    {
        while ($index > 0) {
            $parent = intdiv($index - 1, 2);

            if ($this->priorityComparer->compare($this->heap[$index][1], $this->heap[$parent][1]) >= 0) {
                break;
            }

            $this->swap($index, $parent);
            $index = $parent;
        }
    }

    private function siftDown(int $index): void
    {
        $count = count($this->heap);

        while (true) {
            $left = 2 * $index + 1;
            $right = 2 * $index + 2;
            $smallest = $index;

            if (
                $left < $count
                && $this->priorityComparer->compare($this->heap[$left][1], $this->heap[$smallest][1]) < 0
            ) {
                $smallest = $left;
            }

            if (
                $right < $count
                && $this->priorityComparer->compare($this->heap[$right][1], $this->heap[$smallest][1]) < 0
            ) {
                $smallest = $right;
            }

            if ($smallest === $index) {
                break;
            }

            $this->swap($index, $smallest);
            $index = $smallest;
        }
    }

    private function swap(int $a, int $b): void
    {
        [$this->heap[$a], $this->heap[$b]] = [$this->heap[$b], $this->heap[$a]];
    }
}
