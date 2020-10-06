<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayAccess;
use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Session;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsBsonType;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsStream;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use function array_key_exists;
use function assertArrayHasKey;
use function assertArrayNotHasKey;
use function assertInternalType;
use function assertThat;
use function isInstanceOf;
use function logicalOr;
use function sprintf;

class EntityMap implements ArrayAccess
{
    /** @var array */
    private $map = [];

    /** @var Constraint */
    private static $isSupportedType;

    public function __destruct()
    {
        /* TODO: Determine if this is actually necessary. References to session
         * entities should not persist between tests. */
        foreach ($this->map as $entity) {
            if ($entity->value instanceof Session) {
                $entity->value->endSession();
            }
        }
    }

    /**
     * @see http://php.net/arrayaccess.offsetexists
     */
    public function offsetExists($id)
    {
        assertInternalType('string', $id);

        return array_key_exists($id, $this->map);
    }

    /**
     * @see http://php.net/arrayaccess.offsetget
     */
    public function offsetGet($id)
    {
        assertInternalType('string', $id);
        assertArrayHasKey($id, $this->map, sprintf('No entity is defined for "%s"', $id));

        return $this->map[$id]->value;
    }

    /**
     * @see http://php.net/arrayaccess.offsetset
     */
    public function offsetSet($id, $value)
    {
        Assert::fail('Entities can only be set via register()');
    }

    /**
     * @see http://php.net/arrayaccess.offsetunset
     */
    public function offsetUnset($id)
    {
        Assert::fail('Entities cannot be removed from the map');
    }

    public function set(string $id, $value, string $parentId = null)
    {
        assertArrayNotHasKey($id, $this->map, sprintf('Entity already exists for "%s" and cannot be replaced', $id));
        assertThat($value, self::isSupportedType());

        $parent = $parentId === null ? null : $this->map[$parentId];

        $this->map[$id] = new class ($id, $value, $parent) {
            /** @var string */
            public $id;
            /** @var mixed */
            public $value;
            /** @var self */
            public $parent;

            public function __construct(string $id, $value, self $parent = null)
            {
                $this->id = $id;
                $this->value = $value;
                $this->parent = $parent;
            }

            public function getRoot() : self
            {
                $root = $this;

                while ($root->parent !== null) {
                    $root = $root->parent;
                }

                return $root;
            }
        };
    }

    public function getRootClientIdOf(string $id) : ?string
    {
        $root = $this->map[$id]->getRoot();

        return $root->value instanceof Client ? $root->id : null;
    }

    private static function isSupportedType() : Constraint
    {
        if (self::$isSupportedType === null) {
            self::$isSupportedType = logicalOr(
                isInstanceOf(Client::class),
                isInstanceOf(Database::class),
                isInstanceOf(Collection::class),
                isInstanceOf(Session::class),
                isInstanceOf(Bucket::class),
                isInstanceOf(ChangeStream::class),
                IsBsonType::any(),
                new IsStream()
            );
        }

        return self::$isSupportedType;
    }
}
