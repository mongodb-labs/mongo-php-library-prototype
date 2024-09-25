<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Find;
use TypeError;

class FindTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'allowPartialResults' => self::getInvalidBooleanValues(),
            'batchSize' => self::getInvalidIntegerValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'collation' => self::getInvalidDocumentValues(),
            'cursorType' => self::getInvalidIntegerValues(),
            'hint' => self::getInvalidHintValues(),
            'limit' => self::getInvalidIntegerValues(),
            'max' => self::getInvalidDocumentValues(),
            'maxAwaitTimeMS' => self::getInvalidIntegerValues(),
            'maxScan' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'min' => self::getInvalidDocumentValues(),
            'modifiers' => self::getInvalidDocumentValues(),
            'oplogReplay' => self::getInvalidBooleanValues(),
            'projection' => self::getInvalidDocumentValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'returnKey' => self::getInvalidBooleanValues(),
            'session' => self::getInvalidSessionValues(),
            'showRecordId' => self::getInvalidBooleanValues(),
            'skip' => self::getInvalidIntegerValues(),
            'snapshot' => self::getInvalidBooleanValues(),
            'sort' => self::getInvalidDocumentValues(),
            'typeMap' => self::getInvalidArrayValues(),
        ]);
    }

    public function testSnapshotOptionIsDeprecated(): void
    {
        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => true]);
        });

        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => false]);
        });
    }

    public function testMaxScanOptionIsDeprecated(): void
    {
        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['maxScan' => 1]);
        });
    }

    /** @dataProvider provideInvalidConstructorCursorTypeOptions */
    public function testConstructorCursorTypeOption($cursorType): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['cursorType' => $cursorType]);
    }

    public static function provideInvalidConstructorCursorTypeOptions()
    {
        return self::wrapValuesForDataProvider([-1, 0, 4]);
    }

    public function testExplainableCommandDocument(): void
    {
        // all options except deprecated "snapshot" and "maxScan"
        $options = [
            'allowDiskUse' => true,
            'allowPartialResults' => true,
            'batchSize' => 123,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'limit' => 15,
            'max' => ['x' => 100],
            'maxTimeMS' => 100,
            'min' => ['x' => 10],
            'noCursorTimeout' => true,
            'oplogReplay' => true,
            'projection' => ['_id' => 0],
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'returnKey' => true,
            'showRecordId' => true,
            'skip' => 5,
            'sort' => ['x' => 1],
            'let' => ['y' => 2],
            // Intentionally omitted options
            'cursorType' => Find::NON_TAILABLE,
            'maxAwaitTimeMS' => 500,
            'modifiers' => ['foo' => 'bar'],
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
        ];
        $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);

        $expected = [
            'find' => $this->getCollectionName(),
            'filter' => (object) ['x' => 1],
            'allowDiskUse' => true,
            'allowPartialResults' => true,
            'batchSize' => 123,
            'comment' => 'explain me',
            'hint' => '_id_',
            'limit' => 15,
            'maxTimeMS' => 100,
            'noCursorTimeout' => true,
            'oplogReplay' => true,
            'projection' => ['_id' => 0],
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'returnKey' => true,
            'showRecordId' => true,
            'skip' => 5,
            'sort' => ['x' => 1],
            'collation' => (object) ['locale' => 'fr'],
            'let' => (object) ['y' => 2],
            'max' => (object) ['x' => 100],
            'min' => (object) ['x' => 10],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
