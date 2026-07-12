<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ruslan\Generics\LinkedList;
use UnderflowException;

final class LinkedListTest extends TestCase
{
    public function testAddFirstAndAddLastMaintainOrder(): void
    {
        $list = new LinkedList();
        $list->addLast('b');
        $list->addLast('c');
        $list->addFirst('a');

        self::assertSame(['a', 'b', 'c'], iterator_to_array($list));
        self::assertSame(3, $list->count());
        self::assertSame(3, count($list));
        self::assertSame('a', $list->getFirst()?->value);
        self::assertSame('c', $list->getLast()?->value);
    }

    public function testConstructorAcceptsAnInitialIterable(): void
    {
        $list = new LinkedList([1, 2, 3]);

        self::assertSame([1, 2, 3], iterator_to_array($list));
    }

    public function testAddBeforeAndAddAfter(): void
    {
        $list = new LinkedList(['a', 'c']);
        $middle = $list->find('c');
        self::assertNotNull($middle);

        $list->addBefore($middle, 'b');
        $list->addAfter($middle, 'd');

        self::assertSame(['a', 'b', 'c', 'd'], iterator_to_array($list));
    }

    public function testFindAndFindLastAndContains(): void
    {
        $list = new LinkedList(['a', 'b', 'a']);

        self::assertSame($list->getFirst(), $list->find('a'));
        self::assertSame($list->getLast(), $list->findLast('a'));
        self::assertTrue($list->contains('b'));
        self::assertFalse($list->contains('z'));
        self::assertNull($list->find('z'));
    }

    public function testRemoveByValue(): void
    {
        $list = new LinkedList(['a', 'b', 'c']);

        self::assertTrue($list->remove('b'));
        self::assertSame(['a', 'c'], iterator_to_array($list));
        self::assertFalse($list->remove('z'));
    }

    public function testRemoveNode(): void
    {
        $list = new LinkedList(['a', 'b', 'c']);
        $node = $list->find('b');
        self::assertNotNull($node);

        $list->removeNode($node);

        self::assertSame(['a', 'c'], iterator_to_array($list));
        self::assertNull($node->getList());
    }

    public function testRemoveNodeFromAnotherListThrows(): void
    {
        $list = new LinkedList(['a']);
        $other = new LinkedList(['x']);
        $foreignNode = $other->getFirst();
        self::assertNotNull($foreignNode);

        $this->expectException(InvalidArgumentException::class);

        $list->removeNode($foreignNode);
    }

    public function testRemoveFirstAndRemoveLast(): void
    {
        $list = new LinkedList(['a', 'b', 'c']);

        $list->removeFirst();
        $list->removeLast();

        self::assertSame(['b'], iterator_to_array($list));
    }

    public function testRemoveFirstOnEmptyListThrows(): void
    {
        $list = new LinkedList();

        $this->expectException(UnderflowException::class);

        $list->removeFirst();
    }

    public function testRemoveLastOnEmptyListThrows(): void
    {
        $list = new LinkedList();

        $this->expectException(UnderflowException::class);

        $list->removeLast();
    }

    public function testClearEmptiesTheList(): void
    {
        $list = new LinkedList(['a', 'b']);
        $node = $list->getFirst();
        self::assertNotNull($node);

        $list->clear();

        self::assertSame(0, $list->count());
        self::assertNull($list->getFirst());
        self::assertNull($list->getLast());
        self::assertNull($node->getList());
        self::assertSame([], iterator_to_array($list));
    }
}
