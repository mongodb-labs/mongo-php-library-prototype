<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Deprecated. Use $substrBytes or $substrCP.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/
 * @internal
 */
final class SubstrOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$substr';
    public const PROPERTIES = ['string' => 'string', 'start' => 'start', 'length' => 'length'];

    /** @var ResolvesToString|string $string */
    public readonly ResolvesToString|string $string;

    /** @var ResolvesToInt|int|string $start If start is a negative number, $substr returns an empty string "". */
    public readonly ResolvesToInt|int|string $start;

    /** @var ResolvesToInt|int|string $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string. */
    public readonly ResolvesToInt|int|string $length;

    /**
     * @param ResolvesToString|string $string
     * @param ResolvesToInt|int|string $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int|string $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public function __construct(
        ResolvesToString|string $string,
        ResolvesToInt|int|string $start,
        ResolvesToInt|int|string $length,
    ) {
        $this->string = $string;
        $this->start = $start;
        $this->length = $length;
    }
}
