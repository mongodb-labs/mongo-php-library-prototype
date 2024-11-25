<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test embeddedDocument search
 */
class EmbeddedDocumentOperatorTest extends PipelineTestCase
{
    public function testBasic(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::embeddedDocument(
                    path: 'items',
                    operator:
                        Search::compound(
                            must: [
                                Search::text(
                                    path: 'items.tags',
                                    query: 'school',
                                ),
                            ],
                            should: [
                                Search::text(
                                    path: 'items.name',
                                    query: 'backpack',
                                ),
                            ],
                        ),
                    score: object(
                        embedded: object(
                            aggregate: 'mean',
                        ),
                    ),
                ),
            ),
            Stage::limit(5),
            Stage::project(
                ...[
                    'items.name' => 1,
                    'items.tags' => 1,
                ],
                _id: 0,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::EmbeddedDocumentBasic, $pipeline);
    }

    public function testFacet(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::facet(
                    facets: object(
                        purchaseMethodFacet: object(
                            type: 'string',
                            path: 'purchaseMethod',
                        ),
                    ),
                    operator: Search::embeddedDocument(
                        path: 'items',
                        operator: Search::compound(
                            must: [
                                Search::text(
                                    path: 'items.tags',
                                    query: 'school',
                                ),
                            ],
                            should: [
                                Search::text(
                                    path: 'items.name',
                                    query: 'backpack',
                                ),
                            ],
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EmbeddedDocumentFacet, $pipeline);
    }

    public function testQueryAndSort(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::embeddedDocument(
                    path: 'items',
                    operator:
                        Search::text(
                            path: 'items.name',
                            query: 'laptop',
                        ),
                ),
                sort: ['items.tags' => Sort::Asc],
            ),
            Stage::limit(5),
            Stage::project(
                ...[
                    'items.name' => 1,
                    'items.tags' => 1,
                ],
                _id: 0,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::EmbeddedDocumentQueryAndSort, $pipeline);
    }

    public function testQueryForMatchingEmbeddedDocumentsOnly(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::embeddedDocument(
                    path: 'items',
                    operator:
                        Search::compound(
                            must: [
                                Search::range(
                                    path: 'items.quantity',
                                    gt: 2,
                                ),
                                Search::exists(
                                    path: 'items.price',
                                ),
                                Search::text(
                                    path: 'items.tags',
                                    query: 'school',
                                ),
                            ],
                        ),
                ),
            ),
            Stage::limit(2),
            Stage::project(
                _id: 0,
                storeLocation: 1,
                items: Expression::filter(
                    input: Expression::arrayFieldPath('items'),
                    cond: Expression::and(
                        Expression::ifNull('$$this.price', 'false'),
                        Expression::gt(Expression::variable('this.quantity'), 2),
                        Expression::in('office', Expression::variable('this.tags')),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::EmbeddedDocumentQueryForMatchingEmbeddedDocumentsOnly, $pipeline);
    }
}
