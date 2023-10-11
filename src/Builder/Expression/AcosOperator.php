<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the inverse cosine (arc cosine) of a value in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acos/
 */
class AcosOperator implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$acos';
    public const ENCODE = Encode::Single;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression $acos takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $acos returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acos returns values as a double. $acos can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression $acos takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $acos returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acos returns values as a double. $acos can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
