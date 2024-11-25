<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test autocomplete search
 */
class AutocompleteOperatorTest extends PipelineTestCase
{
    public function testAcrossMultipleFields(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    should: [
                        Search::autocomplete(
                            query: 'inter',
                            path: 'title',
                        ),
                        Search::autocomplete(
                            query: 'inter',
                            path: 'plot',
                        ),
                    ],
                    minimumShouldMatch: 1,
                ),
            ),
            Stage::limit(10),
            Stage::project(_id: 0, title: 1, plot: 1),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteAcrossMultipleFields, $pipeline);
    }

    public function testBasic(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::autocomplete(
                    query: 'off',
                    path: 'title',
                ),
            ),
            Stage::limit(10),
            Stage::project(_id: 0, title: 1),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteBasic, $pipeline);
    }

    public function testFuzzy(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::autocomplete(
                    query: 'pre',
                    path: 'title',
                    fuzzy: object(
                        maxEdits: 1,
                        prefixLength: 1,
                        maxExpansions: 256,
                    ),
                ),
            ),
            Stage::limit(10),
            Stage::project(_id: 0, title: 1),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteFuzzy, $pipeline);
    }

    public function testHighlighting(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::autocomplete(
                    query: 'ger',
                    path: 'title',
                ),
                highlight: object(
                    path: 'title',
                ),
            ),
            Stage::limit(5),
            Stage::project(
                score: ['$meta' => 'searchScore'],
                _id: 0,
                title: 1,
                highlights: ['$meta' => 'searchHighlights'],
            ),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteHighlighting, $pipeline);
    }

    public function testTokenOrderAny(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::autocomplete(
                    query: 'men with',
                    path: 'title',
                    tokenOrder: 'any',
                ),
            ),
            Stage::limit(4),
            Stage::project(_id: 0, title: 1),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteTokenOrderAny, $pipeline);
    }

    public function testTokenOrderSequential(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::autocomplete(
                    query: 'men with',
                    path: 'title',
                    tokenOrder: 'sequential',
                ),
            ),
            Stage::limit(4),
            Stage::project(_id: 0, title: 1),
        );

        $this->assertSamePipeline(Pipelines::AutocompleteTokenOrderSequential, $pipeline);
    }
}
