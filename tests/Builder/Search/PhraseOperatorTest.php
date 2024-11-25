<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test phrase search
 */
class PhraseOperatorTest extends PipelineTestCase
{
    public function testMultiplePhrase(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::phrase(
                    path: 'title',
                    query: [
                        'the man',
                        'the moon',
                    ],
                ),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::PhraseMultiplePhrase, $pipeline);
    }

    public function testPhraseSlop(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::phrase(
                    path: 'title',
                    query: 'men women',
                    slop: 5,
                ),
            ),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::PhrasePhraseSlop, $pipeline);
    }

    public function testPhraseSynonyms(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::phrase(
                    path: 'plot',
                    query: 'automobile race',
                    slop: 5,
                    synonyms: 'my_synonyms',
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

        $this->assertSamePipeline(Pipelines::PhrasePhraseSynonyms, $pipeline);
    }

    public function testSinglePhrase(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::phrase(
                    path: 'title',
                    query: 'new york',
                ),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::PhraseSinglePhrase, $pipeline);
    }
}
