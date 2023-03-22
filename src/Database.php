<?php
/*
 * Copyright 2015-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use Iterator;
use MongoDB\BSON\Binary;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\CreateEncryptedCollectionException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\GridFS\Bucket;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListCollectionNames;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\ModifyCollection;
use MongoDB\Operation\RenameCollection;
use MongoDB\Operation\Watch;
use Traversable;
use Exception;

use function array_key_exists;
use function is_array;
use function is_object;
use function property_exists;
use function strlen;

class Database
{
    /** @var array */
    private static $defaultTypeMap = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    /** @var integer */
    private static $wireVersionForReadConcernWithWriteStage = 8;

    /** @var string */
    private $databaseName;

    /** @var Manager */
    private $manager;

    /** @var ReadConcern */
    private $readConcern;

    /** @var ReadPreference */
    private $readPreference;

    /** @var array */
    private $typeMap;

    /** @var WriteConcern */
    private $writeConcern;

    /**
     * Constructs new Database instance.
     *
     * This class provides methods for database-specific operations and serves
     * as a gateway for accessing collections.
     *
     * Supported options:
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): The default read concern to
     *    use for database operations and selected collections. Defaults to the
     *    Manager's read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Manager's read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Manager's write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Database options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, string $databaseName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        $this->manager = $manager;
        $this->databaseName = $databaseName;
        $this->readConcern = $options['readConcern'] ?? $this->manager->getReadConcern();
        $this->readPreference = $options['readPreference'] ?? $this->manager->getReadPreference();
        $this->typeMap = $options['typeMap'] ?? self::$defaultTypeMap;
        $this->writeConcern = $options['writeConcern'] ?? $this->manager->getWriteConcern();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Select a collection within this database.
     *
     * Note: collections whose names contain special characters (e.g. ".") may
     * be selected with complex syntax (e.g. $database->{"system.profile"}) or
     * {@link selectCollection()}.
     *
     * @see https://php.net/oop5.overloading#object.get
     * @see https://php.net/types.string#language.types.string.parsing.complex
     * @param string $collectionName Name of the collection to select
     * @return Collection
     */
    public function __get(string $collectionName)
    {
        return $this->selectCollection($collectionName);
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->databaseName;
    }

    /**
     * Runs an aggregation framework pipeline on the database for pipeline
     * stages that do not require an underlying collection, such as $currentOp
     * and $listLocalSessions. Requires MongoDB >= 3.6
     *
     * @see Aggregate::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return Traversable
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function aggregate(array $pipeline, array $options = [])
    {
        $hasWriteStage = is_last_pipeline_operator_write($pipeline);

        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = $hasWriteStage
            ? select_server_for_aggregate_write_stage($this->manager, $options)
            : select_server($this->manager, $options);

        /* MongoDB 4.2 and later supports a read concern when an $out stage is
         * being used, but earlier versions do not.
         *
         * A read concern is also not compatible with transactions.
         */
        if (
            ! isset($options['readConcern']) &&
            ! is_in_transaction($options) &&
            ( ! $hasWriteStage || server_supports_feature($server, self::$wireVersionForReadConcernWithWriteStage))
        ) {
            $options['readConcern'] = $this->readConcern;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        if ($hasWriteStage && ! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new Aggregate($this->databaseName, null, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Execute a command on this database.
     *
     * @see DatabaseCommand::__construct() for supported options
     * @param array|object $command Command document
     * @param array        $options Options for command execution
     * @return Cursor
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function command($command, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new DatabaseCommand($this->databaseName, $command, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Create a new collection explicitly.
     *
     * @see CreateCollection::__construct() for supported options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createCollection(string $collectionName, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $encryptedFields = $options['encryptedFields']
            ?? get_encrypted_fields_from_driver($this->databaseName, $collectionName, $this->manager)
            ?? null;

        if ($encryptedFields !== null) {
            // encryptedFields is passed to the create command
            $options['encryptedFields'] = $encryptedFields;

            $encryptedFields = (array) $encryptedFields;
            $enxcolOptions = ['clusteredIndex' => ['key' => ['_id' => 1], 'unique' => true]];
            (new CreateCollection($this->databaseName, $encryptedFields['escCollection'] ?? 'enxcol_.' . $collectionName . '.esc', $enxcolOptions))->execute($server);
            (new CreateCollection($this->databaseName, $encryptedFields['eccCollection'] ?? 'enxcol_.' . $collectionName . '.ecc', $enxcolOptions))->execute($server);
            (new CreateCollection($this->databaseName, $encryptedFields['ecocCollection'] ?? 'enxcol_.' . $collectionName . '.ecoc', $enxcolOptions))->execute($server);
        }

        $operation = new CreateCollection($this->databaseName, $collectionName, $options);

        $result = $operation->execute($server);

        if ($encryptedFields !== null) {
            (new CreateIndexes($this->databaseName, $collectionName, [['key' => ['__safeContent__' => 1]]]))->execute($server);
        }

        return $result;
    }

    /**
     * Create a new encrypted collection explicitly.
     *
     * This function will automatically create data keys for any encrypted
     * fields where the "keyId" option is null. This function will return a copy
     * of the modified "encryptedFields" option in addition to the result from
     * createCollection(). The "encryptedFields" option is required.
     *
     * If any error is encountered while creating data keys or invoking
     * createCollection(), a CreateEncryptedCollectionException will be thrown.
     * The original exception and modified "encryptedFields" option can be
     * accessed via the getPrevious() and getEncryptedFields() methods,
     * respectively.
     *
     * @see CreateCollection::__construct() for supported options
     * @return array A tuple consisting of the createCollection() result and modified "encryptedFields" option
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws CreateEncryptedCollectionException for any errors creating data keys or invoking createCollection()
     */
    public function createEncryptedCollection(string $collectionName, ClientEncryption $clientEncryption, string $kmsProvider, ?array $masterKey, array $options): array
    {
        if (! isset($options['encryptedFields']) || ! is_array($options['encryptedFields']) && ! is_object($options['encryptedFields'])) {
            throw InvalidArgumentException::invalidType('"encryptedFields" option', $options['encryptedFields'] ?? null, ['array', 'object']);
        }

        /** @var array{fields: list<array{keyId: ?Binary}|object{keyId: ?Binary}>} */
        $encryptedFields = (array) recursive_copy($options['encryptedFields']);

        $createDataKeyArgs = [
            $kmsProvider,
            isset($masterKey) ? ['masterKey' => $masterKey] : [],
        ];

        try {
            /** @psalm-suppress RedundantConditionGivenDocblockType */
            if (isset($encryptedFields['fields']) && is_array($encryptedFields['fields'])) {
                foreach ($encryptedFields['fields'] as &$field) {
                    if (is_array($field) && array_key_exists('keyId', $field) && $field['keyId'] === null) {
                        $field['keyId'] = $clientEncryption->createDataKey(...$createDataKeyArgs);
                    } elseif (is_object($field) && property_exists($field, 'keyId') && $field->keyId === null) {
                        $field->keyId = $clientEncryption->createDataKey(...$createDataKeyArgs);
                    }
                }

                $options['encryptedFields'] = $encryptedFields;
            }

            $result = $this->createCollection($collectionName, $options);

            return [$result, $encryptedFields];
        } catch (Exception $e) {
            throw new CreateEncryptedCollectionException($e, $encryptedFields);
        }
    }

    /**
     * Drop this database.
     *
     * @see DropDatabase::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop(array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropDatabase($this->databaseName, $options);

        return $operation->execute($server);
    }

    /**
     * Drop a collection within this database.
     *
     * @see DropCollection::__construct() for supported options
     * @param string $collectionName Collection name
     * @param array  $options        Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropCollection(string $collectionName, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $encryptedFields = $options['encryptedFields']
            ?? get_encrypted_fields_from_driver($this->databaseName, $collectionName, $this->manager)
            ?? get_encrypted_fields_from_server($this->databaseName, $collectionName, $this->manager, $server)
            ?? null;

        if ($encryptedFields !== null) {
            // encryptedFields is not passed to the drop command
            unset($options['encryptedFields']);

            $encryptedFields = (array) $encryptedFields;
            (new DropCollection($this->databaseName, $encryptedFields['escCollection'] ?? 'enxcol_.' . $collectionName . '.esc'))->execute($server);
            (new DropCollection($this->databaseName, $encryptedFields['eccCollection'] ?? 'enxcol_.' . $collectionName . '.ecc'))->execute($server);
            (new DropCollection($this->databaseName, $encryptedFields['ecocCollection'] ?? 'enxcol_.' . $collectionName . '.ecoc'))->execute($server);
        }

        $operation = new DropCollection($this->databaseName, $collectionName, $options);

        return $operation->execute($server);
    }

    /**
     * Returns the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Return the Manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Return the read concern for this database.
     *
     * @see https://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this database.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this database.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this database.
     *
     * @see https://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * Returns the names of all collections in this database
     *
     * @see ListCollectionNames::__construct() for supported options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listCollectionNames(array $options = []): Iterator
    {
        $operation = new ListCollectionNames($this->databaseName, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Returns information for all collections in this database.
     *
     * @see ListCollections::__construct() for supported options
     * @return CollectionInfoIterator
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listCollections(array $options = [])
    {
        $operation = new ListCollections($this->databaseName, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Modifies a collection or view.
     *
     * @see ModifyCollection::__construct() for supported options
     * @param string $collectionName    Collection or view to modify
     * @param array  $collectionOptions Collection or view options to assign
     * @param array  $options           Command options
     * @return array|object
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function modifyCollection(string $collectionName, array $collectionOptions, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new ModifyCollection($this->databaseName, $collectionName, $collectionOptions, $options);

        return $operation->execute($server);
    }

    /**
     * Rename a collection within this database.
     *
     * @see RenameCollection::__construct() for supported options
     * @param string      $fromCollectionName Collection name
     * @param string      $toCollectionName   New name of the collection
     * @param string|null $toDatabaseName     New database name of the collection. Defaults to the original database.
     * @param array       $options            Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function renameCollection(string $fromCollectionName, string $toCollectionName, ?string $toDatabaseName = null, array $options = [])
    {
        if (! isset($toDatabaseName)) {
            $toDatabaseName = $this->databaseName;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new RenameCollection($this->databaseName, $fromCollectionName, $toDatabaseName, $toCollectionName, $options);

        return $operation->execute($server);
    }

    /**
     * Select a collection within this database.
     *
     * @see Collection::__construct() for supported options
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectCollection(string $collectionName, array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Collection($this->manager, $this->databaseName, $collectionName, $options);
    }

    /**
     * Select a GridFS bucket within this database.
     *
     * @see Bucket::__construct() for supported options
     * @param array $options Bucket constructor options
     * @return Bucket
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectGridFSBucket(array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Bucket($this->manager, $this->databaseName, $options);
    }

    /**
     * Create a change stream for watching changes to the database.
     *
     * @see Watch::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return ChangeStream
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array $pipeline = [], array $options = [])
    {
        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['readConcern']) && ! is_in_transaction($options)) {
            $options['readConcern'] = $this->readConcern;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new Watch($this->manager, $this->databaseName, null, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Get a clone of this database with different options.
     *
     * @see Database::__construct() for supported options
     * @param array $options Database constructor options
     * @return Database
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function withOptions(array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Database($this->manager, $this->databaseName, $options);
    }
}
