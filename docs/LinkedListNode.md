# LinkedListNode

A node in a [`LinkedList`](LinkedList.md), holding a value and links to its neighbours. Instances are created by `LinkedList`'s `addFirst()`, `addLast()`, `addBefore()`, and `addAfter()` methods — there's normally no need to construct one directly.

```php
use Ruslan\Generics\LinkedList;

$list = new LinkedList(['a', 'b', 'c']);

$node = $list->getFirst();
$node->value;             // 'a'
$node->getNext()->value;  // 'b'
$node->getPrevious();     // null
```

## Properties

### `value`

The value held by this node. Public and mutable.

## Methods

### `getList(): ?LinkedList`

Returns the list this node belongs to, or `null` if it has been removed (or never inserted).

### `getNext(): ?self`

Returns the next node, or `null` if this is the last node.

### `getPrevious(): ?self`

Returns the previous node, or `null` if this is the first node.

## See also

- [`LinkedList`](LinkedList.md)
