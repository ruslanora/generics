<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use PHPUnit\Framework\TestCase;
use Ruslan\Generics\Queue;
use UnderflowException;

final class QueueTest extends TestCase
{
    public function testEnqueueAndDequeue(): void
    {
        $queue = new Queue(['a']);
        $queue->enqueue('b');

        self::assertSame('a', $queue->dequeue());
        self::assertSame('b', $queue->dequeue());
        self::assertSame(0, $queue->count());
    }

    public function testEnqueueAndPeekDoesNotRemove(): void
    {
        $queue = new Queue(['a']);
        $queue->enqueue('b');

        self::assertSame('a', $queue->peek());
        self::assertSame('a', $queue->peek());
        self::assertSame(2, $queue->count());
    }

    public function testCount(): void
    {
        $queue = new Queue();

        self::assertSame(0, $queue->count());
        self::assertSame(0, count($queue));

        $queue->enqueue('a');

        self::assertSame(1, $queue->count());
        self::assertSame(1, count($queue));
    }

    public function testConstructorAcceptsAnInitialIterableAndEnqueuesInOrder(): void
    {
        $queue = new Queue(['a', 'b', 'c']);

        self::assertSame('a', $queue->dequeue());
        self::assertSame('b', $queue->dequeue());
        self::assertSame('c', $queue->dequeue());
    }

    public function testDequeueOnEmptyQueueThrows(): void
    {
        $queue = new Queue();

        $this->expectException(UnderflowException::class);

        $queue->dequeue();
    }

    public function testPeekOnEmptyQueueThrows(): void
    {
        $queue = new Queue();

        $this->expectException(UnderflowException::class);

        $queue->peek();
    }

    public function testTryDequeue(): void
    {
        $queue = new Queue();
        $queue->enqueue('a');

        self::assertTrue($queue->tryDequeue($value));
        self::assertSame('a', $value);
        self::assertSame(0, $queue->count());

        self::assertFalse($queue->tryDequeue($value));
    }

    public function testTryPeek(): void
    {
        $queue = new Queue(['a']);

        self::assertTrue($queue->tryPeek($value));
        self::assertSame('a', $value);
        self::assertSame(1, $queue->count());

        $queue->dequeue();

        self::assertFalse($queue->tryPeek($value));
    }

    public function testContains(): void
    {
        $queue = new Queue(['a', 'b']);

        self::assertTrue($queue->contains('a'));
        self::assertTrue($queue->contains('b'));
        self::assertFalse($queue->contains('z'));
    }

    public function testClearEmptiesTheQueue(): void
    {
        $queue = new Queue(['a', 'b']);
        $queue->clear();

        self::assertSame(0, $queue->count());
        self::assertSame([], iterator_to_array($queue));
    }

    public function testIterationOrderIsFrontToBack(): void
    {
        $queue = new Queue(['a', 'b', 'c']);

        self::assertSame(['a', 'b', 'c'], iterator_to_array($queue));
    }
}
