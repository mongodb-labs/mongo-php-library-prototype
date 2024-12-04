<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test regex search
 */
class RegexOperatorTest extends PipelineTestCase
{
    public function testRegex(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::regex(
                    path: 'title',
                    query: '[0-9]{2} (.){4}s',
                ),
            ),
            Stage::project(
                _id: 0,
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RegexRegex, $pipeline);
    }
}
