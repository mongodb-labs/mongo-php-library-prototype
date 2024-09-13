<?php

namespace MongoDB\Tests\Command;

use MongoDB\Command\ListCollections;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;

class ListCollectionsTest extends TestCase
{
    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListCollections($this->getDatabaseName(), $options);
    }

    public static function provideInvalidConstructorOptions(): array
    {
        return self::createOptionDataProvider([
            'authorizedCollections' => self::getInvalidBooleanValues(),
            'filter' => self::getInvalidDocumentValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'session' => self::getInvalidSessionValues(),
        ]);
    }
}
