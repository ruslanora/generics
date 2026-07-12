<?php

declare(strict_types=1);

namespace Ruslan\Generics\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ruslan\Generics\Comparer;

final class ComparerTest extends TestCase
{
    public function testDefaultComparesScalarsUsingSpaceshipOperator(): void
    {
        $comparer = Comparer::default();

        self::assertSame(-1, $comparer->compare(1, 2));
        self::assertSame(0, $comparer->compare(2, 2));
        self::assertSame(1, $comparer->compare(2, 1));
        self::assertSame(-1, $comparer->compare('a', 'b'));
    }

    public function testDefaultThrowsWhenValuesAreNotScalar(): void
    {
        $comparer = Comparer::default();

        $this->expectException(InvalidArgumentException::class);

        $comparer->compare([1, 2], [1, 2]);
    }

    public function testCreateWrapsAClosure(): void
    {
        $comparer = Comparer::create(static fn (int $x, int $y): int => $y <=> $x);

        self::assertSame(1, $comparer->compare(1, 2));
        self::assertSame(-1, $comparer->compare(2, 1));
    }

    public function testCreateAcceptsAFirstClassCallable(): void
    {
        $comparer = Comparer::create(strcmp(...));

        self::assertSame(0, $comparer->compare('a', 'a'));
    }
}
