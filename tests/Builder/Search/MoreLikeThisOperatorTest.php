<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\BSON\ObjectId;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test moreLikeThis search
 */
class MoreLikeThisOperatorTest extends PipelineTestCase
{
    public function testInputDocumentExcludedInResults(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: [
                        Search::moreLikeThis(
                            like: object(
                                _id: new ObjectId('573a1396f29313caabce4a9a'),
                                genres: [
                                    'Crime',
                                    'Drama',
                                ],
                                title: 'The Godfather',
                            ),
                        ),
                    ],
                    mustNot: [
                        Search::equals(
                            path: '_id',
                            value: new ObjectId('573a1396f29313caabce4a9a'),
                        ),
                    ],
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 1,
                title: 1,
                released: 1,
                genres: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::MoreLikeThisInputDocumentExcludedInResults, $pipeline);
    }

    public function testMultipleAnalyzers(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    mustNot: [
                        Search::equals(
                            path: '_id',
                            value: new ObjectId('573a1394f29313caabcde9ef'),
                        ),
                    ],
                    should: [
                        Search::moreLikeThis(
                            like: object(
                                _id: new ObjectId('573a1396f29313caabce4a9a'),
                                genres: [
                                    'Crime',
                                    'Drama',
                                ],
                                title: 'The Godfather',
                            ),
                        ),
                    ],
                ),
            ),
            Stage::limit(10),
            Stage::project(
                title: 1,
                genres: 1,
                _id: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::MoreLikeThisMultipleAnalyzers, $pipeline);
    }

    public function testSingleDocumentWithMultipleFields(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::moreLikeThis(
                    like: object(
                        title: 'The Godfather',
                        genres: 'action',
                    ),
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                released: 1,
                genres: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::MoreLikeThisSingleDocumentWithMultipleFields, $pipeline);
    }
}
