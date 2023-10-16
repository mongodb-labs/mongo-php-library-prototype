<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\Optional;
use stdClass;

/**
 * Returns the value of a specified field from a document. You can use $getField to retrieve the value of fields with names that contain periods (.) or start with dollar signs ($).
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/
 */
readonly class GetFieldOperator implements ResolvesToAny
{
    public const NAME = '$getField';
    public const ENCODE = Encode::Object;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     */
    public string $field;

    /**
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public function __construct(
        string $field,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input = Optional::Undefined,
    ) {
        $this->field = $field;
        $this->input = $input;
    }
}
