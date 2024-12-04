<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test text search
 */
class TextOperatorTest extends PipelineTestCase
{
    public function testBasic(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'surfer',
                ),
            ),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextBasic, $pipeline);
    }

    public function testFuzzyDefault(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'naw yark',
                    fuzzy: object(),
                ),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextFuzzyDefault, $pipeline);
    }

    public function testFuzzyMaxExpansions(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'naw yark',
                    fuzzy: object(
                        maxEdits: 1,
                        maxExpansions: 100,
                    ),
                ),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextFuzzyMaxExpansions, $pipeline);
    }

    public function testFuzzyPrefixLength(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'naw yark',
                    fuzzy: object(
                        maxEdits: 1,
                        prefixLength: 2,
                    ),
                ),
            ),
            Stage::limit(8),
            Stage::project(
                _id: 1,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextFuzzyPrefixLength, $pipeline);
    }

    public function testMatchAllUsingSynonyms(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'plot',
                    query: 'automobile race',
                    matchCriteria: 'all',
                    synonyms: 'my_synonyms',
                ),
            ),
            Stage::limit(20),
            Stage::project(
                _id: 0,
                plot: 1,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextMatchAllUsingSynonyms, $pipeline);
    }

    public function testMatchAnyUsingEquivalentMapping(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'plot',
                    query: 'attire',
                    synonyms: 'my_synonyms',
                    matchCriteria: 'any',
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                plot: 1,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextMatchAnyUsingEquivalentMapping, $pipeline);
    }

    public function testMatchAnyUsingExplicitMapping(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'plot',
                    query: 'boat race',
                    synonyms: 'my_synonyms',
                    matchCriteria: 'any',
                ),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                plot: 1,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextMatchAnyUsingExplicitMapping, $pipeline);
    }

    public function testWildcardPath(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: ['wildcard' => '*'],
                    query: 'surfer',
                ),
            ),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::TextWildcardPath, $pipeline);
    }
}
