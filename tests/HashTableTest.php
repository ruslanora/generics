<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Ruslan\Generics\HashTable;

final class HashTableTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $table = new HashTable();
        $table->set('a', 1);
        $table->set('b', 2);

        self::assertSame(1, $table->get('a'));
        self::assertSame(2, $table->get('b'));
        self::assertSame(2, $table->count());
        self::assertSame(2, count($table));
    }

    public function testSetOverwritesAnExistingKey(): void
    {
        $table = new HashTable();
        $table->set('a', 1);
        $table->set('a', 2);

        self::assertSame(2, $table->get('a'));
        self::assertSame(1, $table->count());
    }

    public function testAddThrowsWhenKeyAlreadyExists(): void
    {
        $table = new HashTable();
        $table->add('a', 1);

        $this->expectException(InvalidArgumentException::class);

        $table->add('a', 2);
    }

    public function testGetThrowsWhenKeyIsMissing(): void
    {
        $table = new HashTable();

        $this->expectException(OutOfBoundsException::class);

        $table->get('missing');
    }

    public function testTryGet(): void
    {
        $table = new HashTable();
        $table->set('a', 1);

        self::assertTrue($table->tryGet('a', $value));
        self::assertSame(1, $value);
        self::assertFalse($table->tryGet('missing', $value));
    }

    public function testHasAndRemove(): void
    {
        $table = new HashTable();
        $table->set('a', 1);

        self::assertTrue($table->has('a'));
        self::assertTrue($table->remove('a'));
        self::assertFalse($table->has('a'));
        self::assertFalse($table->remove('a'));
    }

    public function testClear(): void
    {
        $table = new HashTable([['a', 1], ['b', 2]]);
        $table->clear();

        self::assertSame(0, $table->count());
        self::assertFalse($table->has('a'));
    }

    public function testConstructorAcceptsTuples(): void
    {
        $table = new HashTable([['a', 1], ['b', 2]]);

        self::assertSame(1, $table->get('a'));
        self::assertSame(2, $table->get('b'));
    }

    public function testKeysAndValues(): void
    {
        $table = new HashTable([['a', 1], ['b', 2]]);

        self::assertEqualsCanonicalizing(['a', 'b'], iterator_to_array($table->keys(), false));
        self::assertEqualsCanonicalizing([1, 2], iterator_to_array($table->values(), false));
    }

    public function testIterationYieldsEveryKeyValueTupleRegardlessOfOrder(): void
    {
        $table = new HashTable([['a', 1], ['b', 2]]);

        self::assertEqualsCanonicalizing([['a', 1], ['b', 2]], iterator_to_array($table));
    }

    public function testArrayAccess(): void
    {
        $table = new HashTable();
        $table['a'] = 1;

        self::assertTrue(isset($table['a']));
        self::assertSame(1, $table['a']);

        unset($table['a']);

        self::assertFalse(isset($table['a']));
    }

    public function testArrayAccessAppendWithoutKeyThrows(): void
    {
        $table = new HashTable();

        $this->expectException(InvalidArgumentException::class);

        $table[] = 1;
    }

    public function testObjectKeysAreDistinguishedByIdentity(): void
    {
        $keyA = new class () {};
        $keyB = new class () {};

        $table = new HashTable();
        $table->set($keyA, 'a');
        $table->set($keyB, 'b');

        self::assertSame('a', $table->get($keyA));
        self::assertSame('b', $table->get($keyB));
        self::assertSame(2, $table->count());
    }

    public function testDistinctKeyTypesDoNotCollide(): void
    {
        $table = new HashTable();
        $table->set(1, 'int');
        $table->set('1', 'string');
        $table->set(1.0, 'float');
        $table->set(true, 'bool');

        self::assertSame('int', $table->get(1));
        self::assertSame('string', $table->get('1'));
        self::assertSame('float', $table->get(1.0));
        self::assertSame('bool', $table->get(true));
        self::assertSame(4, $table->count());
    }

    public function testNanAsAKeyDoesNotEmitAWarning(): void
    {
        $table = new HashTable();
        $table->set(NAN, 'first');
        $table->set(NAN, 'second');

        self::assertSame('second', $table->get(NAN));
        self::assertSame(1, $table->count());
    }

    public function testNullKeyThrows(): void
    {
        $table = new HashTable();

        $this->expectException(InvalidArgumentException::class);

        $table->set(null, 'value');
    }

    public function testSurvivesGrowingWellBeyondInitialCapacity(): void
    {
        $table = new HashTable();

        for ($i = 0; $i < 500; $i++) {
            $table->set('key-' . $i, $i);
        }

        self::assertSame(500, $table->count());

        for ($i = 0; $i < 500; $i++) {
            self::assertTrue($table->has('key-' . $i));
            self::assertSame($i, $table->get('key-' . $i));
        }

        $table->set('key-0', 'updated');
        self::assertSame('updated', $table->get('key-0'));
        self::assertSame(500, $table->count());

        for ($i = 1; $i < 500; $i += 2) {
            self::assertTrue($table->remove('key-' . $i));
        }

        self::assertSame(250, $table->count());
        self::assertTrue($table->has('key-0'));
        self::assertFalse($table->has('key-1'));
    }
}
