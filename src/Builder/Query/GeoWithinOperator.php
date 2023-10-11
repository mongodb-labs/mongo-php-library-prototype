<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\QueryFilterInterface;
use stdClass;

/**
 * Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support $geoWithin.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/
 */
class GeoWithinOperator implements QueryFilterInterface
{
    public const NAME = '$geoWithin';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|GeometryInterface|Serializable|array|stdClass $geometry */
    public Document|Serializable|GeometryInterface|stdClass|array $geometry;

    /**
     * @param Document|GeometryInterface|Serializable|array|stdClass $geometry
     */
    public function __construct(Document|Serializable|GeometryInterface|stdClass|array $geometry)
    {
        $this->geometry = $geometry;
    }
}
