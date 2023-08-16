<?php

namespace MongoDB\Benchmark\BSON;

use MongoDB\Benchmark\BaseBench;
use MongoDB\BSON\Document;
use PhpBench\Attributes\BeforeMethods;

use function file_get_contents;
use function iterator_to_array;

#[BeforeMethods('prepareData')]
final class DocumentBench extends BaseBench
{
    private static Document $document;

    public function prepareData(): void
    {
        self::$document = Document::fromJSON(file_get_contents(self::LARGE_FILE_PATH));
    }

    public function benchCheckFirst(): void
    {
        self::$document->has('qx3MigjubFSm');
    }

    public function benchCheckLast(): void
    {
        self::$document->has('Zz2MOlCxDhLl');
    }

    public function benchAccessFirst(): void
    {
        self::$document->get('qx3MigjubFSm');
    }

    public function benchAccessLast(): void
    {
        self::$document->get('Zz2MOlCxDhLl');
    }

    public function benchIteratorToArray(): void
    {
        iterator_to_array(self::$document);
    }

    public function benchToPHPObject(): void
    {
        self::$document->toPHP();
    }

    public function benchToPHPArray(): void
    {
        self::$document->toPHP(['root' => 'array']);
    }

    public function benchIteration(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::$document as $key => $value);
    }

    public function benchIterationAsArray(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::$document->toPHP(['root' => 'array']) as $key => $value);
    }

    public function benchIterationAsObject(): void
    {
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedForeach
        foreach (self::$document->toPHP() as $key => $value);
    }
}
