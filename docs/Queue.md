# Queue

A FIFO (first-in-first-out) collection backed by a plain PHP array. Iteration runs front to back — the same order values would come out via `dequeue()` — matching the enumeration order of .NET's `Queue<T>`.

```php
use Ruslan\Generics\Queue;

/** @var Queue<int> $queue */
$queue = new Queue([1, 2, 3]);

$queue->enqueue(4);

$queue->peek();    // 1
$queue->dequeue(); // 1

foreach ($queue as $value) {
    // 2, 3, 4
}
```

## Constructor

### `__construct(iterable $values = [])`

Creates a queue, enqueuing each value from `$values` in order.

## Methods

### `count(): int`

Number of elements currently in the queue.

### `enqueue(mixed $value): void`

Adds a value to the back of the queue.

### `dequeue(): mixed`

Removes and returns the value at the front of the queue.

Throws `UnderflowException` if the queue is empty.

### `peek(): mixed`

Returns the value at the front of the queue without removing it.

Throws `UnderflowException` if the queue is empty.

### `tryDequeue(mixed &$value): bool`

Attempts to dequeue the front value into `$value`. Returns `true` on success, `false` if the queue is empty, without throwing.

### `tryPeek(mixed &$value): bool`

Attempts to read the front value into `$value` without removing it. Returns `true` on success, `false` if the queue is empty, without throwing.

### `contains(mixed $value): bool`

Returns `true` if `$value` is present anywhere in the queue, compared with `===`.

### `clear(): void`

Removes all elements.

## Iteration

`Queue` implements `IteratorAggregate` and `Countable`. Iterating yields values from front to back.
