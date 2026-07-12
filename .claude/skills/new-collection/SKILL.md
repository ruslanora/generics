---
name: new-collection
description: Scaffold a new generic collection class (Stack, Queue, PriorityQueue, HashTable, etc.) for this library, test-first, following the conventions established by HashMap and LinkedList. Use whenever asked to add, implement, or start a new collection type in src/Generics.
---

# New collection

This library (`Ruslan\Generics\`) implements PHP ports of `System.Collections.Generic` types. Every collection is
built test-first and must match the conventions already established by `HashMap` and `LinkedList`. Read both before
starting if you haven't recently — they are the reference implementations, not just prior art.

## Steps

1. **Design the public API first**, in .NET terms translated to PHP idiom. Check the equivalent
   `System.Collections.Generic` type's member list (e.g. `Stack<T>.Push`/`Pop`/`Peek`, `Queue<T>.Enqueue`/`Dequeue`/
   `Peek`, `PriorityQueue<TElement,TPriority>.Enqueue`/`Dequeue`/`Peek`) and adapt naming to what's already used in
   this repo (`addFirst`/`addLast`/`removeFirst` on `LinkedList`, `set`/`add`/`get`/`tryGet`/`has`/`remove` on
   `HashMap`). Don't invent new naming schemes — reuse verbs already established here over literal C# names when
   they conflict.
2. **Write the test file first**, in `tests/<Name>Test.php`, namespace `Ruslan\Generics\Tests`. Follow
   `tests/HashMapTest.php` / `tests/LinkedListTest.php` for granularity: one narrowly-named `testXxx` method per
   behavior, not broad combined tests. Explicitly cover:
   - the happy path for every public method
   - empty-collection errors (e.g. popping/dequeuing/peeking empty — use `UnderflowException`, matching
     `LinkedList::removeFirst()`)
   - `count()` / `Countable` behavior
   - iteration order via `IteratorAggregate` (`iterator_to_array($collection)`)
   - any type-identity edge cases analogous to `HashMap`'s object-key and NaN-key tests, if the collection is keyed
3. **Implement the class** in `src/Generics/<Name>.php`:
   - `final class`, `declare(strict_types=1);`, namespace `Ruslan\Generics`
   - `@template` tag(s) at the class level for every generic parameter; `@param T`/`@return T` PHPDoc on members
     using them. Native PHP signatures stay untyped where a template type is involved — PHPStan enforces the
     generics, not the PHP type system.
   - Implement `Countable` and `IteratorAggregate` (and `ArrayAccess` only if the collection is naturally keyed, as
     `HashMap` is). `getIterator()` is a `Generator` method — no separate iterator class.
   - No shared collection base class — each type stands alone, matching `HashMap`/`LinkedList`.
   - If the collection needs to compare elements (e.g. `PriorityQueue` ordering), take a `ComparerInterface` and
     default to `Comparer::default()` — don't invent a second comparison abstraction.
   - Prefer SPL exceptions over custom ones: `InvalidArgumentException` for bad arguments,
     `OutOfBoundsException` for a missing-key lookup, `UnderflowException` for removing/peeking from empty. Only
     add a class under `src/Generics/Exceptions/` if no SPL exception fits.
   - Add a short class-level doc comment explaining any non-obvious *why* (see the doc comments atop `HashMap` and
     `LinkedList` for the expected tone — one paragraph, justifying a design choice, not restating the class name).
4. **Verify**: run `composer test`, `composer analyse`, and `composer format:check` (or `composer format` to
   autofix style) and fix everything before considering the collection done. PHPStan runs at `level: max` and
   PHPUnit fails on risky/warning tests, so don't treat either as optional.

## When adapting a C# member that doesn't fit PHP

If a `System.Collections.Generic` member relies on something PHP lacks cleanly (e.g. `TryPeek(out T)` style out
params), mirror the pattern `HashMap::tryGet()` already uses: a `bool`-returning method with a by-reference
`&$value` out param and `@param-out` PHPDoc, rather than returning a nullable or throwing.
