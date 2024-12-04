<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test exists search
 */
class ExistsOperatorTest extends PipelineTestCase
{
    public function testBasic(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::exists(
                    path: 'type',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExistsBasic, $pipeline);
    }

    public function testCompound(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::exists(
                            path: 'type',
                        ),
                        Search::text(
                            path: 'type',
                            query: 'apple',
                        ),
                    ],
                    should: Search::text(
                        path: 'description',
                        query: 'fuji',
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExistsCompound, $pipeline);
    }

    public function testEmbedded(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::exists(
                    path: 'quantities.lemons',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExistsEmbedded, $pipeline);
    }
}
