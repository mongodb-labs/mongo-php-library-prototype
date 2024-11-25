<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test queryString search
 */
class QueryStringOperatorTest extends PipelineTestCase
{
    public function testBooleanOperatorQueries(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::queryString(
                    defaultPath: 'title',
                    query: 'Rocky AND (IV OR 4 OR Four)',
                ),
            ),
            Stage::project(_id: 0, title: 1),
        );

        $this->assertSamePipeline(Pipelines::QueryStringBooleanOperatorQueries, $pipeline);
    }
}
