<?php

declare(strict_types=1);

namespace Ruslan\Generics\Interfaces;

/**
 * Defines a method that a type implements to compare two objects.
 *
 * @template-contravariant T
 */
interface ComparerInterface
{
    /**
     * @param T $x
     * @param T $y
     *
     * @return int A negative number if $x precedes $y, zero if they are equal,
     *             a positive number if $x follows $y.
     */
    public function compare($x, $y): int;
}
