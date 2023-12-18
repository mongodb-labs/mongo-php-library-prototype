<?php

/**
 * For applications that need to interact with GridFS using only a filename string,
 * a bucket can be registered with an alias. Files can then be accessed using the
 * following pattern: gridfs://<bucket-alias>/<filename>
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function stream_context_create;

use const PHP_EOL;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');
$bucket = $client->test->selectGridFSBucket();
$bucket->drop();

// Register the alias "mybucket" for default bucket of the "test" database
$bucket->registerGlobalStreamWrapperAlias('mybucket');

echo 'File exists: ';
echo file_exists('gridfs://mybucket/hello.txt') ? 'yes' : 'no';
echo PHP_EOL;

echo 'Writing file';
file_put_contents('gridfs://mybucket/hello.txt', 'Hello, GridFS!');
echo PHP_EOL;

echo 'File exists: ';
echo file_exists('gridfs://mybucket/hello.txt') ? 'yes' : 'no';
echo PHP_EOL;

echo 'Reading file: ';
echo file_get_contents('gridfs://mybucket/hello.txt');
echo PHP_EOL;

echo 'Writing new version of the file';
file_put_contents('gridfs://mybucket/hello.txt', 'Hello, GridFS! (v2)');
echo PHP_EOL;

echo 'Reading new version of the file: ';
echo file_get_contents('gridfs://mybucket/hello.txt');
echo PHP_EOL;

echo 'Reading previous version of the file: ';
$context = stream_context_create(['gridfs' => ['revision' => -2]]);
echo file_get_contents('gridfs://mybucket/hello.txt', false, $context);
echo PHP_EOL;
