# LinkedList

A doubly linked list. Unlike an array-backed list, inserting or removing next to a node you already hold is O(1) — that's the whole reason to reach for this over a plain array.

```php
use Ruslan\Generics\LinkedList;

/** @var LinkedList<string> $list */
$list = new LinkedList(['b', 'c']);

$first = $list->addFirst('a'); // list is now: a, b, c
$list->addAfter($first, 'a2'); // a, a2, b, c

foreach ($list as $value) {
    // a, a2, b, c
}

$list->remove('b');
$list->count(); // 3
```

Each mutating method that inserts a value returns the [`LinkedListNode`](LinkedListNode.md) that was created, so you can hold onto it for O(1) insertion or removal later:

```php
$node = $list->addLast('d');
$list->addBefore($node, 'c2'); // ..., c2, d
$list->removeNode($node);      // ..., c2
```

## Constructor

### `__construct(iterable $values = [])`

Creates a linked list, appending each value from `$values` in order via `addLast()`.

## Methods

### `count(): int`

Number of elements currently in the list.

### `getFirst(): ?LinkedListNode`

Returns the first node, or `null` if the list is empty.

### `getLast(): ?LinkedListNode`

Returns the last node, or `null` if the list is empty.

### `addFirst(mixed $value): LinkedListNode`

Inserts `$value` at the head of the list and returns its node.

### `addLast(mixed $value): LinkedListNode`

Inserts `$value` at the tail of the list and returns its node.

### `addBefore(LinkedListNode $node, mixed $value): LinkedListNode`

Inserts `$value` immediately before `$node` and returns its node.

Throws `InvalidArgumentException` if `$node` does not belong to this list.

### `addAfter(LinkedListNode $node, mixed $value): LinkedListNode`

Inserts `$value` immediately after `$node` and returns its node.

Throws `InvalidArgumentException` if `$node` does not belong to this list.

### `removeNode(LinkedListNode $node): void`

Removes `$node` from the list.

Throws `InvalidArgumentException` if `$node` does not belong to this list.

### `removeFirst(): void`

Removes the first node.

Throws `UnderflowException` if the list is empty.

### `removeLast(): void`

Removes the last node.

Throws `UnderflowException` if the list is empty.

### `remove(mixed $value): bool`

Finds and removes the first node whose value equals `$value` (`===`). Returns `true` if a node was removed, `false` otherwise.

### `contains(mixed $value): bool`

Returns `true` if `$value` is present anywhere in the list, compared with `===`.

### `find(mixed $value): ?LinkedListNode`

Returns the first node whose value equals `$value`, searching from the head, or `null` if not found.

### `findLast(mixed $value): ?LinkedListNode`

Returns the first node whose value equals `$value`, searching from the tail, or `null` if not found.

### `clear(): void`

Removes all elements.

## Iteration

`LinkedList` implements `IteratorAggregate` and `Countable`. Iterating yields values from head to tail.

## See also

- [`LinkedListNode`](LinkedListNode.md)
