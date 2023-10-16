<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Calculates the population standard deviation of the input values. Use if the values encompass the entire population of data you want to represent and do not wish to generalize about a larger population. $stdDevPop ignores non-numeric values.
 * If the values represent only a sample of a population of data from which to generalize about the population, use $stdDevSamp instead.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/
 */
readonly class StdDevPopOperator implements ResolvesToDouble
{
    public const NAME = '$stdDevPop';
    public const ENCODE = Encode::Single;

    /** @param list<Decimal128|Int64|ResolvesToNumber|float|int> ...$expression */
    public array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     * @no-named-arguments
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
