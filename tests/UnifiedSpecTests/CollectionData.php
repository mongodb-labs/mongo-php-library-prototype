<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\UnifiedSpecTests\Constraint\DocumentsMatch;
use ArrayIterator;
use IteratorIterator;
use MultipleIterator;
use stdClass;

class CollectionData
{
    private $collectionName;
    private $databaseName;
    private $documents;

    public function __construct(stdClass $o)
    {
        assertIsString($o->collectionName);
        $this->collectionName = $o->collectionName;

        assertIsString($o->databaseName);
        $this->databaseName = $o->databaseName;

        assertIsArray($o->documents);
        assertContainsOnly('object', $o->documents);
        $this->documents = $o->documents;
    }

    /**
     * Prepare collection state for "initialData".
     *
     * @param Client $client
     */
    public function prepare(Client $client)
    {
        $database = $client->selectDatabase(
            $this->databaseName,
            ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]
        );

        $database->dropCollection($this->collectionName);

        if (empty($this->documents)) {
            $database->createCollection($this->collectionName);
            return;
        }

        $collection = $database->selectCollection($this->collectionName);
        $collection->insertMany($this->documents);
    }

    /**
     * Assert collection contents for "outcome".
     *
     * @param Client $client
     */
    public function assertOutcome(Client $client)
    {
        $collection = $client->selectCollection(
            $this->databaseName,
            $this->collectionName,
            [
                'readConcern' => new ReadConcern(ReadConcern::LOCAL),
                'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
            ]
        );

        $cursor = $collection->find([], ['sort' => ['_id' => 1]]);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->documents));
        $mi->attachIterator(new IteratorIterator($cursor));

        foreach ($mi as $i => $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            assertNotNull($expectedDocument);
            assertNotNull($actualDocument);

            $constraint = new DocumentsMatch($expectedDocument, false, false);
            assertThat($actualDocument, $constraint, sprintf('documents[%d] match', $i));
        }
    }
}
