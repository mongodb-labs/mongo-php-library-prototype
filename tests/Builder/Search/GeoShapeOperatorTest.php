<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test geoShape search
 */
class GeoShapeOperatorTest extends PipelineTestCase
{
    public function testDisjoint(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::geoShape(
                    relation: 'disjoint',
                    geometry: object(
                        type: 'Polygon',
                        coordinates: [
                            [
                                [
                                    -161.323242,
                                    22.512557,
                                ],
                                [
                                    -152.446289,
                                    22.065278,
                                ],
                                [
                                    -156.09375,
                                    17.811456,
                                ],
                                [
                                    -161.323242,
                                    22.512557,
                                ],
                            ],
                        ],
                    ),
                    path: 'address.location',
                ),
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                name: 1,
                address: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoShapeDisjoint, $pipeline);
    }

    public function testIntersect(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::geoShape(
                    relation: 'intersects',
                    geometry: object(
                        type: 'MultiPolygon',
                        coordinates: [
                            [
                                [
                                    [
                                        2.16942,
                                        41.40082,
                                    ],
                                    [
                                        2.17963,
                                        41.40087,
                                    ],
                                    [
                                        2.18146,
                                        41.39716,
                                    ],
                                    [
                                        2.15533,
                                        41.40686,
                                    ],
                                    [
                                        2.14596,
                                        41.38475,
                                    ],
                                    [
                                        2.17519,
                                        41.41035,
                                    ],
                                    [
                                        2.16942,
                                        41.40082,
                                    ],
                                ],
                            ],
                            [
                                [
                                    [
                                        2.16365,
                                        41.39416,
                                    ],
                                    [
                                        2.16963,
                                        41.39726,
                                    ],
                                    [
                                        2.15395,
                                        41.38005,
                                    ],
                                    [
                                        2.17935,
                                        41.43038,
                                    ],
                                    [
                                        2.16365,
                                        41.39416,
                                    ],
                                ],
                            ],
                        ],
                    ),
                    path: 'address.location',
                ),
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                name: 1,
                address: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoShapeIntersect, $pipeline);
    }

    public function testWithin(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::geoShape(
                    relation: 'within',
                    geometry: object(
                        type: 'Polygon',
                        coordinates: [
                            [
                                [
                                    -74.3994140625,
                                    40.5305017757,
                                ],
                                [
                                    -74.7290039063,
                                    40.5805846641,
                                ],
                                [
                                    -74.7729492188,
                                    40.9467136651,
                                ],
                                [
                                    -74.0698242188,
                                    41.1290213475,
                                ],
                                [
                                    -73.65234375,
                                    40.9964840144,
                                ],
                                [
                                    -72.6416015625,
                                    40.9467136651,
                                ],
                                [
                                    -72.3559570313,
                                    40.7971774152,
                                ],
                                [
                                    -74.3994140625,
                                    40.5305017757,
                                ],
                            ],
                        ],
                    ),
                    path: 'address.location',
                ),
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                name: 1,
                address: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoShapeWithin, $pipeline);
    }
}
