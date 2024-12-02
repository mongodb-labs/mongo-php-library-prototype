<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Converts value to an integer.
 * New in MongoDB 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/
 * @internal
 */
final class ToIntOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$toInt';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }
}
