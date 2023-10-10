<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

/**
 * Constructs a BSON Date object given the date's constituent parts.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/
 */
class DateFromPartsOperator implements ResolvesToDate
{
    public const NAME = '$dateFromParts';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $year;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeekYear;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $month Month. Defaults to 1. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $month;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeek;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $day Day of month. Defaults to 1. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $day;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoDayOfWeek;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $hour Hour. Defaults to 0. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $hour;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $minute Minute. Defaults to 0. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $minute;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $second Second. Defaults to 0. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $second;

    /** @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0. */
    public Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $millisecond;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public Optional|ResolvesToString|string $timezone;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number.
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $month Month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $day Day of month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $hour Hour. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $minute Minute. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $second Second. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $year,
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeekYear,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $month = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $day = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $isoDayOfWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $hour = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $minute = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $second = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $millisecond = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ) {
        $this->year = $year;
        $this->isoWeekYear = $isoWeekYear;
        $this->month = $month;
        $this->isoWeek = $isoWeek;
        $this->day = $day;
        $this->isoDayOfWeek = $isoDayOfWeek;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->millisecond = $millisecond;
        $this->timezone = $timezone;
    }
}
