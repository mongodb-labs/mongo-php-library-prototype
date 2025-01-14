<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Calculates the natural log of a number.
 * $ln is equivalent to $log: [ <number>, Math.E ] expression, where Math.E is a JavaScript representation for Euler's number e.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/
 * @internal
 */
final class LnOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$ln';
    public const PROPERTIES = ['number' => 'number'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int $number;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $number)
    {
        $this->number = $number;
    }
}
