<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

/**
 * Returns true if the first value is greater than or equal to the second.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/
 */
class GteAggregation implements ResolvesToBool
{
    public const NAME = '$gte';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ExpressionInterface|mixed $expression1 */
    public mixed $expression1;

    /** @param ExpressionInterface|mixed $expression2 */
    public mixed $expression2;

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public function __construct(mixed $expression1, mixed $expression2)
    {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
