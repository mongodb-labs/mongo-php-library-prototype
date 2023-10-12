<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Returns a random float between 0 and 1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/
 */
readonly class RandOperator implements ResolvesToDouble
{
    public const NAME = '$rand';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
