<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;

/**
 * Deprecated. Use $substrBytes or $substrCP.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/
 */
class SubstrAggregation implements ResolvesToString
{
    public const NAME = '$substr';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ResolvesToString|non-empty-string $string */
    public ResolvesToString|string $string;

    /** @param Int64|ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "". */
    public Int64|ResolvesToInt|int $start;

    /** @param Int64|ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string. */
    public Int64|ResolvesToInt|int $length;

    /**
     * @param ResolvesToString|non-empty-string $string
     * @param Int64|ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param Int64|ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public function __construct(
        ResolvesToString|string $string,
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $length,
    ) {
        $this->string = $string;
        $this->start = $start;
        $this->length = $length;
    }
}