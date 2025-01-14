<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

use function is_scalar;
use function json_encode;
use function sort;
use function usort;

use const JSON_THROW_ON_ERROR;

class DistinctFunctionalTest extends FunctionalTestCase
{
    #[DataProvider('provideFilterDocuments')]
    public function testFilterDocuments($filter, stdClass $expectedQuery): void
    {
        (new CommandObserver())->observe(
            function () use ($filter): void {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    $filter,
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedQuery): void {
                $this->assertEquals($expectedQuery, $event['started']->getCommand()->query ?? null);
            },
        );
    }

    public function testDefaultReadConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasProperty('readConcern', $event['started']->getCommand());
            },
        );
    }

    public function testHintOption(): void
    {
        $this->skipIfServerVersion('<', '7.1.0', 'hint is not supported');

        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 1],
            ['x' => 2, 'y' => 2],
            ['y' => 3],
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [
            ['key' => ['x' => 1], 'sparse' => true, 'name' => 'sparse_x'],
            ['key' => ['y' => 1]],
        ]);
        $createIndexes->execute($this->getPrimaryServer());

        $hintsUsingSparseIndex = [
            ['x' => 1],
            'sparse_x',
        ];

        foreach ($hintsUsingSparseIndex as $hint) {
            $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'y', [], ['hint' => $hint]);
            $this->assertSame([2], $operation->execute($this->getPrimaryServer()));
        }

        $hintsNotUsingSparseIndex = [
            ['_id' => 1],
            ['y' => 1],
            'y_1',
        ];

        foreach ($hintsNotUsingSparseIndex as $hint) {
            $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', [], ['hint' => $hint]);
            $values = $operation->execute($this->getPrimaryServer());
            sort($values);
            $this->assertSame([1, 2], $values);
        }
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['session' => $this->createSession()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasProperty('lsid', $event['started']->getCommand());
            },
        );
    }

    #[DataProvider('provideTypeMapOptionsAndExpectedDocuments')]
    public function testTypeMapOption(array $typeMap, array $expectedDocuments): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => 'bar'],
        ]);
        $bulkWrite->insert(['x' => 4]);
        $bulkWrite->insert([
            'x' => (object) ['foo' => ['foo' => 'bar']],
        ]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $distinct = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', [], ['typeMap' => $typeMap]);
        $values = $distinct->execute($this->getPrimaryServer());

        /* This sort callable sorts all scalars to the front of the list. All
         * non-scalar values are sorted by running json_encode on them and
         * comparing their string representations.
         */
        $sort = function ($a, $b) {
            if (is_scalar($a) && ! is_scalar($b)) {
                return -1;
            }

            if (! is_scalar($a)) {
                if (is_scalar($b)) {
                    return 1;
                }

                $a = json_encode($a, JSON_THROW_ON_ERROR);
                $b = json_encode($b, JSON_THROW_ON_ERROR);
            }

            return $a < $b ? -1 : 1;
        };

        usort($expectedDocuments, $sort);
        usort($values, $sort);

        $this->assertEquals($expectedDocuments, $values);
    }

    public static function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            'No type map' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/array' => [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => ['foo' => 'bar']],
                ],
            ],
            'object/array' => [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['foo' => 'bar'],
                    4,
                    (object) ['foo' => ['foo' => 'bar']],
                ],
            ],
            'array/stdClass' => [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['foo' => 'bar'],
                    4,
                    ['foo' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }
}
