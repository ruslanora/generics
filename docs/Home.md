# Generics Collections in PHP

Type-safe collections for PHP, powered by PHPStan generics, inspired by the [C# Generics Collection](https://learn.microsoft.com/en-us/dotnet/api/system.collections.generic) library.

## Classes

- [Stack](Stack.md) — a LIFO (last-in-first-out) collection.
- [Queue](Queue.md) — a FIFO (first-in-first-out) collection.
- [PriorityQueue](PriorityQueue.md) — a queue where elements are dequeued in priority order.
- [LinkedList](LinkedList.md) — a doubly linked list.
  - [LinkedListNode](LinkedListNode.md) — a node in a `LinkedList`.
- [HashMap](HashMap.md) — a map keyed by any type, backed by a native PHP array.
- [HashTable](HashTable.md) — a map keyed by any type, built on separate chaining.
- [Comparer](Comparer.md) — a base class for defining custom comparison logic.
  - [ComparerInterface](ComparerInterface.md) — the interface `Comparer` implements.

## Installation

```sh
composer require ruslanora/generics
```

## Contributing

See [CONTRIBUTING.md](https://github.com/ruslanora/generics/blob/main/CONTRIBUTING.md) in the main repository.
