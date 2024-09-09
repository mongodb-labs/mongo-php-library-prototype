<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DatabaseCommand;
use TypeError;

class DatabaseCommandTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorCommandArgumentTypeCheck($command): void
    {
        $this->expectException($command instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new DatabaseCommand($this->getDatabaseName(), $command);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DatabaseCommand($this->getDatabaseName(), ['ping' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'readPreference' => $this->getInvalidReadPreferenceValues(),
            'session' => $this->getInvalidSessionValues(),
            'typeMap' => $this->getInvalidArrayValues(),
        ]);
    }
}
