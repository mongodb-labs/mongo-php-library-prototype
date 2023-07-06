<?php

/**
  * @generate-class-entries static
  * @generate-function-entries static
  */

namespace MongoDB\BSON;

/**
 * @psalm-template TKey of int|string
 * @psalm-template TValue
 * $psalm-implements \Iterator<TKey, TValue>
 */
final class Iterator implements \Iterator
{
    final private function __construct() {}

    /** @return TValue */
    final public function current() {}

    /** @return TKey */
    final public function key() {}

    final public function next(): void {}

    final public function rewind(): void {}

    final public function valid(): bool {}

    final public function __wakeup(): void {}
}
