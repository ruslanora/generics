# PriorityQueue

A binary min-heap keyed by priority: `dequeue()` always returns the element with the lowest priority first, according to the given [`Comparer`](Comparer.md) (or `Comparer::default()` if none is given).

Iteration walks the heap's internal array layout rather than dequeue order — the same tradeoff .NET's `PriorityQueue<TElement,TPriority>` makes with `UnorderedItems` — because producing sorted output would mean either draining the heap or sorting a copy on every iteration, and nothing about a heap promises iteration order anyway.

```php
use Ruslan\Generics\PriorityQueue;

/** @var PriorityQueue<string, int> $queue */
$queue = new PriorityQueue();

$queue->enqueue('do dishes', 2);
$queue->enqueue('put out fire', 1);
$queue->enqueue('water plants', 3);

$queue->dequeue(); // 'put out fire' (priority 1)
$queue->dequeue(); // 'do dishes' (priority 2)
```

By default, priorities are compared with the spaceship operator (`<=>`), so they must be scalars. Pass a custom comparer to sort by anything else:

```php
use Ruslan\Generics\Comparer;
use Ruslan\Generics\PriorityQueue;

// Max-heap: highest priority first.
$queue = new PriorityQueue(priorityComparer: Comparer::create(
    fn (int $a, int $b): int => $b <=> $a,
));
```

## Constructor

### `__construct(iterable $items = [], ?ComparerInterface $priorityComparer = null)`

Creates a priority queue. `$items` is an iterable of `[$element, $priority]` tuples, each enqueued in order. `$priorityComparer` compares priorities; defaults to `Comparer::default()`.

## Methods

### `count(): int`

Number of elements currently in the queue.

### `enqueue(mixed $element, mixed $priority): void`

Adds `$element` with the given `$priority`.

### `dequeue(): mixed`

Removes and returns the element with the lowest priority.

Throws `UnderflowException` if the queue is empty.

### `peek(): mixed`

Returns the element with the lowest priority without removing it.

Throws `UnderflowException` if the queue is empty.

### `tryDequeue(mixed &$element, mixed &$priority): bool`

Attempts to dequeue the lowest-priority element and its priority into `$element` and `$priority`. Returns `true` on success, `false` if the queue is empty, without throwing.

### `tryPeek(mixed &$element, mixed &$priority): bool`

Attempts to read the lowest-priority element and its priority into `$element` and `$priority` without removing it. Returns `true` on success, `false` if the queue is empty, without throwing.

### `enqueueDequeue(mixed $element, mixed $priority): mixed`

Equivalent to `enqueue()` followed by `dequeue()`, but skips the redundant work when the new element would just be dequeued straight back out (i.e. its priority is not lower than the current root's).

### `clear(): void`

Removes all elements.

## Iteration

`PriorityQueue` implements `IteratorAggregate` and `Countable`. Iterating yields `[$element, $priority]` tuples in heap order, **not** priority order.
