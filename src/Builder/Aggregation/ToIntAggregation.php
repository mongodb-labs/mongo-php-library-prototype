<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToInt;

/**
 * Converts value to an integer.
 * New in version 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/
 */
class ToIntAggregation implements ResolvesToInt
{
    public const NAME = '$toInt';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ExpressionInterface|mixed $expression */
    public mixed $expression;

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
