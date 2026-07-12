<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Ruslan\Generics\HashMap;

final class HashMapTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $map = new HashMap();
        $map->set('a', 1);
        $map->set('b', 2);

        self::assertSame(1, $map->get('a'));
        self::assertSame(2, $map->get('b'));
        self::assertSame(2, $map->count());
        self::assertSame(2, count($map));
    }

    public function testSetOverwritesAnExistingKey(): void
    {
        $map = new HashMap();
        $map->set('a', 1);
        $map->set('a', 2);

        self::assertSame(2, $map->get('a'));
        self::assertSame(1, $map->count());
    }

    public function testAddThrowsWhenKeyAlreadyExists(): void
    {
        $map = new HashMap();
        $map->add('a', 1);

        $this->expectException(InvalidArgumentException::class);

        $map->add('a', 2);
    }

    public function testGetThrowsWhenKeyIsMissing(): void
    {
        $map = new HashMap();

        $this->expectException(OutOfBoundsException::class);

        $map->get('missing');
    }

    public function testTryGet(): void
    {
        $map = new HashMap();
        $map->set('a', 1);

        self::assertTrue($map->tryGet('a', $value));
        self::assertSame(1, $value);
        self::assertFalse($map->tryGet('missing', $value));
    }

    public function testHasAndRemove(): void
    {
        $map = new HashMap();
        $map->set('a', 1);

        self::assertTrue($map->has('a'));
        self::assertTrue($map->remove('a'));
        self::assertFalse($map->has('a'));
        self::assertFalse($map->remove('a'));
    }

    public function testClear(): void
    {
        $map = new HashMap([['a', 1], ['b', 2]]);
        $map->clear();

        self::assertSame(0, $map->count());
    }

    public function testConstructorAcceptsTuples(): void
    {
        $map = new HashMap([['a', 1], ['b', 2]]);

        self::assertSame(1, $map->get('a'));
        self::assertSame(2, $map->get('b'));
    }

    public function testKeysAndValues(): void
    {
        $map = new HashMap([['a', 1], ['b', 2]]);

        self::assertSame(['a', 'b'], iterator_to_array($map->keys(), false));
        self::assertSame([1, 2], iterator_to_array($map->values(), false));
    }

    public function testIterationYieldsKeyValueTuples(): void
    {
        $map = new HashMap([['a', 1], ['b', 2]]);

        $seen = [];
        foreach ($map as [$key, $value]) {
            $seen[] = [$key, $value];
        }

        self::assertSame([['a', 1], ['b', 2]], $seen);
    }

    public function testArrayAccess(): void
    {
        $map = new HashMap();
        $map['a'] = 1;

        self::assertTrue(isset($map['a']));
        self::assertSame(1, $map['a']);

        unset($map['a']);

        self::assertFalse(isset($map['a']));
    }

    public function testArrayAccessAppendWithoutKeyThrows(): void
    {
        $map = new HashMap();

        $this->expectException(InvalidArgumentException::class);

        $map[] = 1;
    }

    public function testObjectKeysAreDistinguishedByIdentity(): void
    {
        $keyA = new class () {};
        $keyB = new class () {};

        $map = new HashMap();
        $map->set($keyA, 'a');
        $map->set($keyB, 'b');

        self::assertSame('a', $map->get($keyA));
        self::assertSame('b', $map->get($keyB));
        self::assertSame(2, $map->count());
    }

    public function testDistinctKeyTypesDoNotCollide(): void
    {
        $map = new HashMap();
        $map->set(1, 'int');
        $map->set('1', 'string');
        $map->set(1.0, 'float');
        $map->set(true, 'bool');

        self::assertSame('int', $map->get(1));
        self::assertSame('string', $map->get('1'));
        self::assertSame('float', $map->get(1.0));
        self::assertSame('bool', $map->get(true));
        self::assertSame(4, $map->count());
    }

    public function testNanAsAKeyDoesNotEmitAWarning(): void
    {
        $map = new HashMap();
        $map->set(NAN, 'first');
        $map->set(NAN, 'second');

        self::assertSame('second', $map->get(NAN));
        self::assertSame(1, $map->count());
    }

    public function testNullKeyThrows(): void
    {
        $map = new HashMap();

        $this->expectException(InvalidArgumentException::class);

        $map->set(null, 'value');
    }
}
