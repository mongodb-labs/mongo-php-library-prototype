<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Inverts the effect of a query expression and returns documents that do not match the query expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
 * @internal
 */
final class NotOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$not';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var FieldQueryInterface|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param FieldQueryInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }
}
