<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\FieldPathInterface;

readonly class DoubleFieldPath implements FieldPathInterface, ResolvesToDouble
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
