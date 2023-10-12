<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;

/**
 * Calculates the sample standard deviation of the input values. Use if the values encompass a sample of a population of data from which to generalize about the population. $stdDevSamp ignores non-numeric values.
 * If the values represent the entire population of data or you do not wish to generalize about a larger population, use $stdDevPop instead.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/
 */
readonly class StdDevSampAccumulator implements AccumulatorInterface
{
    public const NAME = '$stdDevSamp';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
