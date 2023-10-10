<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the average rate of change within the specified window.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/
 */
class DerivativeOperator implements WindowInterface
{
    public const NAME = '$derivative';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToDate|ResolvesToInt|ResolvesToNumber|UTCDateTime|float|int $input */
    public Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToInt|ResolvesToNumber|float|int $input;

    /**
     * @param Optional|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public Optional|string $unit;

    /**
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToInt|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public function __construct(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToInt|ResolvesToNumber|float|int $input,
        Optional|string $unit = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->unit = $unit;
    }
}