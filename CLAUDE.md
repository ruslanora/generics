# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A PHP library implementing strongly-typed collections modeled on .NET's `System.Collections.Generic` (e.g.
`Dictionary<TKey,TValue>`, `LinkedList<T>`, `Stack<T>`, `Queue<T>`, `PriorityQueue<TElement,TPriority>`). PHP has no
real generics, so type parameters are expressed with `@template` PHPDoc annotations and enforced by PHPStan rather
than the language itself.

Planned collections: `HashMap`, `HashTable`, `LinkedList` (done), `Stack`, `Queue`, `PriorityQueue`. When asked to
add a new one, use the `new-collection` skill (`.claude/skills/new-collection/SKILL.md`) — it captures the test-first
scaffolding workflow and naming/exception conventions in detail.

## Commands

```bash
composer test          # run the full PHPUnit suite
composer analyse        # run PHPStan (level: max)
composer format          # auto-fix code style with php-cs-fixer
composer format:check    # check code style without modifying files (CI mode)
```

Run a single test file or method directly with PHPUnit:

```bash
vendor/bin/phpunit tests/HashMapTest.php
vendor/bin/phpunit --filter testSetOverwritesAnExistingKey
```

Always run `composer test` and `composer analyse` before considering a change complete — PHPStan runs at `level: max`
across both `src` and `tests`, and PHPUnit is configured with `failOnRisky` and `failOnWarning`, so silently-broken
or low-quality tests fail the build.

## Architecture

- Namespace root `Ruslan\Generics\` maps to `src/Generics/` (PSR-4); tests live under `Ruslan\Generics\Tests\` in
  `tests/`, mirroring the source tree one-to-one (`src/Generics/Foo.php` ↔ `tests/FooTest.php`).
- Each collection is a single `final class` implementing the relevant SPL interfaces (`Countable`,
  `IteratorAggregate`, `ArrayAccess`) rather than extending a shared base — there is no common collection base
  class. `IteratorAggregate::getIterator()` is implemented as a `Generator` method, not a separate iterator class.
- Generic typing is done entirely via `@template` tags on the class plus `@param T`/`@return T` on members; native
  PHP parameter/return types stay untyped (`mixed`/no hint) where a template type is involved, since PHP itself
  can't express them. PHPStan is what actually checks generic usage — always keep annotations in sync with behavior.
- `Comparer` (`src/Generics/Comparer.php`) is the shared abstraction for pluggable ordering, used by anything that
  needs to compare elements (e.g. `PriorityQueue`). It implements `Interfaces\ComparerInterface` and is instantiated
  via named constructors (`Comparer::default()` for spaceship-operator comparison of scalars, `Comparer::create()`
  to wrap a closure) rather than `new`, using anonymous classes internally.
- `HashMap` supports arbitrary key types (including objects, by identity) by hashing each key to an `int|string`
  bucket key via a private `bucketKeyFor()` method, then storing `[key, value]` tuples so the original key is
  recoverable. Because of this, iterating a `HashMap` yields `[key, value]` tuples (not PHP `key => value` pairs) —
  a real key would have to collapse to `int|string` for native iteration, breaking object keys. Follow this
  tuple-yielding convention for any other keyed collection (e.g. `HashTable`).
  - Distinguish `set()` (upsert) from `add()` (throws `InvalidArgumentException` if the key exists) — both are
    expected on keyed collections.
  - Distinguish `get()` (throws `OutOfBoundsException` if missing) from `tryGet()` (by-ref out param, returns
    `bool`) — both are expected where a single fetch might fail.
- `LinkedList` is doubly-linked; `LinkedListNode` holds `next`/`prev`/`list` links. Node mutation methods
  (`attachTo`, `setNext`, `setPrevious`, `detach`) are public but documented `@internal` — they exist only for
  `LinkedList` to call, not for outside code. A node knows which list owns it (`getList()`), and mutating methods
  that take a node (`addBefore`, `addAfter`, `removeNode`) must assert ownership via `assertOwnedByThis()` and throw
  `InvalidArgumentException` for a foreign node.
- Prefer standard SPL exceptions over custom ones (`InvalidArgumentException`, `OutOfBoundsException`,
  `UnderflowException` are already in use for bad arguments, missing keys, and removal from an empty collection,
  respectively). `src/Generics/Exceptions/` exists for future custom exceptions but is currently empty.

## Code style

- `declare(strict_types=1);` in every file, PSR-4 autoloading, PSR-12 + PSR-12-risky enforced by php-cs-fixer
  (short array syntax, ordered/no-unused imports, trailing commas in multiline — see `.php-cs-fixer.dist.php`).
- Doc comments on non-obvious methods explain *why*, not what (see the module-level doc comments on `HashMap` and
  `LinkedList`) — keep that style rather than restating the method signature in prose.
- This project is test-driven: write/update PHPUnit tests alongside behavior changes. Tests favor many small,
  narrowly-named `testXxx` methods per behavior (including edge cases like null/NaN/object keys, empty-collection
  errors, and foreign-node errors) over broad combined tests — follow the existing test files' granularity.
