<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use stdClass;
use function array_diff;
use function in_array;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function version_compare;

class RunOnRequirement
{
    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';
    const TOPOLOGY_SHARDED_REPLICASET = 'sharded-replicaset';
    const TOPOLOGY_LOAD_BALANCED = 'load-balanced';

    const VERSION_PATTERN = '/^[0-9]+(\\.[0-9]+){1,2}$/';

    /** @var string */
    private $minServerVersion;

    /** @var string */
    private $maxServerVersion;

    /** @var array */
    private $topologies;

    /** @var stdClass */
    private $serverParameters;

    /** @var bool */
    private $auth;

    /** @var array */
    private static $supportedTopologies = [
        self::TOPOLOGY_SINGLE,
        self::TOPOLOGY_REPLICASET,
        self::TOPOLOGY_SHARDED,
        self::TOPOLOGY_SHARDED_REPLICASET,
        self::TOPOLOGY_LOAD_BALANCED,
    ];

    public function __construct(stdClass $o)
    {
        Util::assertHasOnlyKeys($o, ['minServerVersion', 'maxServerVersion', 'topologies', 'serverParameters', 'auth']);

        if (isset($o->minServerVersion)) {
            assertIsString($o->minServerVersion);
            assertMatchesRegularExpression(self::VERSION_PATTERN, $o->minServerVersion);
            $this->minServerVersion = $o->minServerVersion;
        }

        if (isset($o->maxServerVersion)) {
            assertIsString($o->maxServerVersion);
            assertMatchesRegularExpression(self::VERSION_PATTERN, $o->maxServerVersion);
            $this->maxServerVersion = $o->maxServerVersion;
        }

        if (isset($o->topologies)) {
            assertIsArray($o->topologies);
            assertContainsOnly('string', $o->topologies);
            assertEmpty(array_diff($o->topologies, self::$supportedTopologies));
            $this->topologies = $o->topologies;
        }

        if (isset($o->serverParameters)) {
            assertIsObject($o->serverParameters);
            $this->serverParameters = $o->serverParameters;
        }

        if (isset($o->auth)) {
            assertIsBool($o->auth);
            $this->auth = $o->auth;
        }
    }

    public function isSatisfied(string $serverVersion, string $topology, stdClass $serverParameters, bool $isAuthenticated) : bool
    {
        if (isset($this->minServerVersion) && version_compare($serverVersion, $this->minServerVersion, '<')) {
            return false;
        }

        if (isset($this->maxServerVersion) && version_compare($serverVersion, $this->maxServerVersion, '>')) {
            return false;
        }

        if (isset($this->topologies) && ! $this->isTopologySatisfied($topology)) {
            return false;
        }

        if (isset($this->serverParameters)) {
            $constraint = new Matches($this->serverParameters, null, true, false);
            if (! $constraint->evaluate($serverParameters, '', true)) {
                return false;
            }
        }

        if (isset($this->auth) && $isAuthenticated !== $this->auth) {
            return false;
        }

        return true;
    }

    private function isTopologySatisfied(string $topology) : bool
    {
        if (in_array($topology, $this->topologies)) {
            return true;
        }

        /* Ensure "sharded-replicaset" is also accepted for topologies that
         * only include "sharded" (agnostic about the shard topology) */
        if ($topology === self::TOPOLOGY_SHARDED_REPLICASET && in_array(self::TOPOLOGY_SHARDED, $this->topologies)) {
            return true;
        }

        return false;
    }
}
