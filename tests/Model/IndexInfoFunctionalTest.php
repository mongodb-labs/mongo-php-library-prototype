<?php

namespace MongoDB\Tests\Model;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase;

class IndexInfoFunctionalTest extends FunctionalTestCase
{
    private Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
    }

    public function testIs2dSphere(): void
    {
        $indexName = $this->collection->createIndex(['pos' => '2dsphere']);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->is2dSphere());

        // MongoDB 3.2+ reports index version 3
        $this->assertEquals(3, $index['2dsphereIndexVersion']);
    }

    public function testIsText(): void
    {
        $indexName = $this->collection->createIndex(['x' => 'text']);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->isText());
        $this->assertEquals('english', $index['default_language']);
        $this->assertEquals('language', $index['language_override']);

        // MongoDB 3.2+ reports index version 3
        $this->assertEquals(3, $index['textIndexVersion']);

        $this->assertSameDocument(['x' => 1], $index['weights']);
    }
}
