<?php

declare(strict_types=1);

namespace Ruslan\Generics;

use Closure;
use InvalidArgumentException;
use Ruslan\Generics\Interfaces\ComparerInterface;

/**
 * Base class for a generic comparer.
 *
 * @template T
 *
 * @implements ComparerInterface<T>
 */
abstract class Comparer implements ComparerInterface
{
    /**
     * Compares scalar values with the spaceship operator. Anything else (objects,
     * arrays, ...) has no meaningful natural order here, so use create() instead.
     *
     * @return self<T>
     */
    public static function default(): self
    {
        return new class () extends Comparer {
            public function compare($x, $y): int
            {
                if (!is_scalar($x) || !is_scalar($y)) {
                    throw new InvalidArgumentException(sprintf(
                        'Cannot compare %s and %s; pass an explicit comparer to Comparer::create().',
                        get_debug_type($x),
                        get_debug_type($y),
                    ));
                }

                return $x <=> $y;
            }
        };
    }

    /**
     * @template U
     *
     * @param Closure(U, U): int $comparison
     *
     * @return self<U>
     */
    public static function create(Closure $comparison): self
    {
        return new class ($comparison) extends Comparer {
            /**
             * @param Closure(U, U): int $comparison
             */
            public function __construct(private readonly Closure $comparison)
            {
            }

            public function compare($x, $y): int
            {
                return ($this->comparison)($x, $y);
            }
        };
    }

    /**
     * @param T $x
     * @param T $y
     */
    abstract public function compare($x, $y): int;
}
