<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test compound search
 */
class CompoundOperatorTest extends PipelineTestCase
{
    public function testFilter(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::text(
                            query: 'varieties',
                            path: 'description',
                        ),
                    ],
                    should: [
                        Search::text(
                            query: 'banana',
                            path: 'description',
                        ),
                    ],
                    filter: [
                        Search::text(
                            query: 'granny',
                            path: 'description',
                        ),
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CompoundFilter, $pipeline);
    }

    public function testMinimumShouldMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::text(
                            path: 'description',
                            query: 'varieties',
                        ),
                    ],
                    should: [
                        Search::text(
                            path: 'description',
                            query: 'Fuji',
                        ),
                        Search::text(
                            path: 'description',
                            query: 'Golden Delicious',
                        ),
                    ],
                    minimumShouldMatch: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CompoundMinimumShouldMatch, $pipeline);
    }

    public function testMustAndMustNot(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::text(
                            path: 'description',
                            query: 'varieties',
                        ),
                    ],
                    mustNot: [
                        Search::text(
                            path: 'description',
                            query: 'apples',
                        ),
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CompoundMustAndMustNot, $pipeline);
    }

    public function testMustAndShould(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::text(
                            path: 'description',
                            query: 'varieties',
                        ),
                    ],
                    should: [
                        Search::text(
                            path: 'description',
                            query: 'Fuji',
                        ),
                    ],
                ),
            ),
            Stage::project(
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::CompoundMustAndShould, $pipeline);
    }

    public function testNested(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    should: [
                        Search::text(
                            path: 'type',
                            query: 'apple',
                        ),
                        Search::compound(
                            must: [
                                Search::text(
                                    path: 'category',
                                    query: 'organic',
                                ),
                                Search::equals(
                                    path: 'in_stock',
                                    value: true,
                                ),
                            ],
                        ),
                    ],
                    minimumShouldMatch: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CompoundNested, $pipeline);
    }
}
