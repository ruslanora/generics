# Stack

A LIFO (last-in-first-out) collection backed by a plain PHP array. Iteration runs top to bottom — the most recently pushed value first — matching the enumeration order of .NET's `Stack<T>` rather than insertion order.

```php
use Ruslan\Generics\Stack;

/** @var Stack<int> $stack */
$stack = new Stack([1, 2, 3]);

$stack->push(4);

$stack->peek(); // 4
$stack->pop();  // 4

foreach ($stack as $value) {
    // 3, 2, 1
}
```

## Constructor

### `__construct(iterable $values = [])`

Creates a stack, pushing each value from `$values` in order.

## Methods

### `count(): int`

Number of elements currently on the stack.

### `push(mixed $value): void`

Pushes a value onto the top of the stack.

### `pop(): mixed`

Removes and returns the value at the top of the stack.

Throws `UnderflowException` if the stack is empty.

### `peek(): mixed`

Returns the value at the top of the stack without removing it.

Throws `UnderflowException` if the stack is empty.

### `tryPop(mixed &$value): bool`

Attempts to pop the top value into `$value`. Returns `true` on success, `false` if the stack is empty, without throwing.

### `tryPeek(mixed &$value): bool`

Attempts to read the top value into `$value` without removing it. Returns `true` on success, `false` if the stack is empty, without throwing.

### `contains(mixed $value): bool`

Returns `true` if `$value` is present anywhere in the stack, compared with `===`.

### `clear(): void`

Removes all elements.

## Iteration

`Stack` implements `IteratorAggregate` and `Countable`. Iterating yields values from the top of the stack to the bottom.
