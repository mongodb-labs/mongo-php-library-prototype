<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropCollection;
use PHPUnit\Framework\Attributes\DataProvider;

class DropCollectionTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'session' => self::getInvalidSessionValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
