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
 * Returns the inverse hyperbolic sine (hyperbolic arc sine) of a value in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/
 * @internal
 */
final class AsinhOperator implements ResolvesToDouble, ResolvesToDecimal, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$asinh';
    public const PROPERTIES = ['expression' => 'expression'];

    /**
     * @var Decimal128|Int64|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
