<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

/**
 * Lists sampled queries for all collections or a specific collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/
 */
class ListSampledQueriesStage implements StageInterface
{
    public const NAME = '$listSampledQueries';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Optional|non-empty-string $namespace */
    public Optional|string $namespace;

    /**
     * @param Optional|non-empty-string $namespace
     */
    public function __construct(Optional|string $namespace = Optional::Undefined)
    {
        $this->namespace = $namespace;
    }
}