<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use stdClass;

/**
 * Reorders the document stream by a specified sort key. Only the order changes; the documents remain unmodified. For each input document, outputs one document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/
 */
class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array|stdClass $sort */
    public stdClass|array $sort;

    /**
     * @param array|stdClass $sort
     */
    public function __construct(stdClass|array $sort)
    {
        $this->sort = $sort;
    }
}