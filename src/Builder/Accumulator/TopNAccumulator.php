<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Returns an aggregation of the top n fields within a group, according to the specified sort order.
 * New in MongoDB 5.2.
 *
 * Available in the $group and $setWindowFields stages.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/
 * @internal
 */
final class TopNAccumulator implements AccumulatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$topN';
    public const PROPERTIES = ['n' => 'n', 'sortBy' => 'sortBy', 'output' => 'output'];

    /** @var ResolvesToInt|int|string $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group. */
    public readonly ResolvesToInt|int|string $n;

    /** @var Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public readonly Document|Serializable|stdClass|array $sortBy;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the group and can be any expression. */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param ResolvesToInt|int|string $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(
        ResolvesToInt|int|string $n,
        Document|Serializable|stdClass|array $sortBy,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
    ) {
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
