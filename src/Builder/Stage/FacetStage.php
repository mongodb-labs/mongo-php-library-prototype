<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Pipeline;
use stdClass;

/**
 * Processes multiple aggregation pipelines within a single stage on the same set of input documents. Enables the creation of multi-faceted aggregations capable of characterizing data across multiple dimensions, or facets, in a single stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/facet/
 */
class FacetStage implements StageInterface
{
    public const NAME = '$facet';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param stdClass<Pipeline|array> ...$facet */
    public stdClass $facet;

    /**
     * @param Pipeline|array ...$facet
     */
    public function __construct(Pipeline|array ...$facet)
    {
        if (\count($facet) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $facet, got %d.', 1, \count($facet)));
        }
        foreach($facet as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $facet arguments to be a map of Pipeline|array, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        $facet = (object) $facet;
        $this->facet = $facet;
    }
}
