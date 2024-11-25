<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test wildcard search
 */
class WildcardOperatorTest extends PipelineTestCase
{
    public function testEscapeCharacterExample(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::wildcard(
                    query: '*\\?',
                    path: 'title',
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::WildcardEscapeCharacterExample, $pipeline);
    }

    public function testWildcardPath(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::wildcard(
                    query: 'Wom?n *',
                    path: ['wildcard' => '*'],
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::WildcardWildcardPath, $pipeline);
    }
}
