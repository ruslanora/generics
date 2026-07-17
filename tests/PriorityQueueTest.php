<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ruslan\Generics\Comparer;
use Ruslan\Generics\PriorityQueue;
use UnderflowException;

final class PriorityQueueTest extends TestCase
{
    public function testEnqueueAndDequeueReturnsLowestPriorityFirst(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('c', 3);
        $queue->enqueue('a', 1);
        $queue->enqueue('b', 2);

        self::assertSame('a', $queue->dequeue());
        self::assertSame('b', $queue->dequeue());
        self::assertSame('c', $queue->dequeue());
        self::assertSame(0, $queue->count());
    }

    public function testEnqueueAndPeekDoesNotRemove(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('b', 2);
        $queue->enqueue('a', 1);

        self::assertSame('a', $queue->peek());
        self::assertSame('a', $queue->peek());
        self::assertSame(2, $queue->count());
    }

    public function testCount(): void
    {
        $queue = new PriorityQueue();

        self::assertSame(0, $queue->count());
        self::assertSame(0, count($queue));

        $queue->enqueue('a', 1);

        self::assertSame(1, $queue->count());
        self::assertSame(1, count($queue));
    }

    public function testConstructorAcceptsAnInitialIterableOfElementPriorityTuples(): void
    {
        $queue = new PriorityQueue([['c', 3], ['a', 1], ['b', 2]]);

        self::assertSame('a', $queue->dequeue());
        self::assertSame('b', $queue->dequeue());
        self::assertSame('c', $queue->dequeue());
    }

    public function testDequeueOnEmptyQueueThrows(): void
    {
        $queue = new PriorityQueue();

        $this->expectException(UnderflowException::class);

        $queue->dequeue();
    }

    public function testPeekOnEmptyQueueThrows(): void
    {
        $queue = new PriorityQueue();

        $this->expectException(UnderflowException::class);

        $queue->peek();
    }

    public function testTryDequeue(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('a', 1);

        self::assertTrue($queue->tryDequeue($element, $priority));
        self::assertSame('a', $element);
        self::assertSame(1, $priority);
        self::assertSame(0, $queue->count());

        self::assertFalse($queue->tryDequeue($element, $priority));
    }

    public function testTryPeek(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('a', 1);

        self::assertTrue($queue->tryPeek($element, $priority));
        self::assertSame('a', $element);
        self::assertSame(1, $priority);
        self::assertSame(1, $queue->count());

        $queue->dequeue();

        self::assertFalse($queue->tryPeek($element, $priority));
    }

    public function testEnqueueDequeueReturnsTheNewElementWhenItsPriorityIsLowest(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('b', 2);

        self::assertSame('a', $queue->enqueueDequeue('a', 1));
        self::assertSame('b', $queue->dequeue());
        self::assertSame(0, $queue->count());
    }

    public function testEnqueueDequeueReturnsTheExistingRootWhenItsPriorityIsLower(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('a', 1);

        self::assertSame('a', $queue->enqueueDequeue('b', 2));
        self::assertSame('b', $queue->dequeue());
        self::assertSame(0, $queue->count());
    }

    public function testClearEmptiesTheQueue(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('a', 1);
        $queue->enqueue('b', 2);
        $queue->clear();

        self::assertSame(0, $queue->count());
        self::assertSame([], iterator_to_array($queue));
    }

    public function testIterationYieldsEveryElementPriorityTupleRegardlessOfOrder(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('c', 3);
        $queue->enqueue('a', 1);
        $queue->enqueue('b', 2);

        $tuples = iterator_to_array($queue);

        self::assertCount(3, $tuples);
        self::assertEqualsCanonicalizing(
            [['c', 3], ['a', 1], ['b', 2]],
            $tuples,
        );
    }

    public function testCustomComparerReversesDequeueOrder(): void
    {
        $queue = new PriorityQueue([], Comparer::create(static fn (int $x, int $y): int => $y <=> $x));
        $queue->enqueue('a', 1);
        $queue->enqueue('c', 3);
        $queue->enqueue('b', 2);

        self::assertSame('c', $queue->dequeue());
        self::assertSame('b', $queue->dequeue());
        self::assertSame('a', $queue->dequeue());
    }

    public function testDefaultComparerThrowsWhenPrioritiesAreNotScalar(): void
    {
        $queue = new PriorityQueue();
        $queue->enqueue('a', [1]);

        $this->expectException(InvalidArgumentException::class);

        $queue->enqueue('b', [2]);
    }
}
