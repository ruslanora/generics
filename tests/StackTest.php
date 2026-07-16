<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use PHPUnit\Framework\TestCase;
use Ruslan\Generics\Stack;
use UnderflowException;

final class StackTest extends TestCase
{
    public function testPushAndPop(): void
    {
        $stack = new Stack(['a']);
        $stack->push('b');

        self::assertSame('b', $stack->pop());
        self::assertSame('a', $stack->pop());
        self::assertSame(0, $stack->count());
    }

    public function testPushAndPeekDoesNotRemove(): void
    {
        $stack = new Stack(['a']);
        $stack->push('b');

        self::assertSame('b', $stack->peek());
        self::assertSame('b', $stack->peek());
        self::assertSame(2, $stack->count());
    }

    public function testCount(): void
    {
        $stack = new Stack();

        self::assertSame(0, $stack->count());
        self::assertSame(0, count($stack));

        $stack->push('a');

        self::assertSame(1, $stack->count());
        self::assertSame(1, count($stack));
    }

    public function testConstructorAcceptsAnInitialIterableAndPushesInOrder(): void
    {
        $stack = new Stack(['a', 'b', 'c']);

        self::assertSame('c', $stack->pop());
        self::assertSame('b', $stack->pop());
        self::assertSame('a', $stack->pop());
    }

    public function testPopOnEmptyStackThrows(): void
    {
        $stack = new Stack();

        $this->expectException(UnderflowException::class);

        $stack->pop();
    }

    public function testPeekOnEmptyStackThrows(): void
    {
        $stack = new Stack();

        $this->expectException(UnderflowException::class);

        $stack->peek();
    }

    public function testTryPop(): void
    {
        $stack = new Stack();
        $stack->push('a');

        self::assertTrue($stack->tryPop($value));
        self::assertSame('a', $value);
        self::assertSame(0, $stack->count());

        self::assertFalse($stack->tryPop($value));
    }

    public function testTryPeek(): void
    {
        $stack = new Stack(['a']);

        self::assertTrue($stack->tryPeek($value));
        self::assertSame('a', $value);
        self::assertSame(1, $stack->count());

        $stack->pop();

        self::assertFalse($stack->tryPeek($value));
    }

    public function testContains(): void
    {
        $stack = new Stack(['a', 'b']);

        self::assertTrue($stack->contains('a'));
        self::assertTrue($stack->contains('b'));
        self::assertFalse($stack->contains('z'));
    }

    public function testClearEmptiesTheStack(): void
    {
        $stack = new Stack(['a', 'b']);
        $stack->clear();

        self::assertSame(0, $stack->count());
        self::assertSame([], iterator_to_array($stack));
    }

    public function testIterationOrderIsTopToBottom(): void
    {
        $stack = new Stack(['a', 'b', 'c']);

        self::assertSame(['c', 'b', 'a'], iterator_to_array($stack));
    }
}
