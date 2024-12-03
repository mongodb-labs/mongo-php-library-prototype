<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

/**
 * Marker interface for MongoDB operators.
 */
interface OperatorInterface
{
    /** @var Encode */
    public const ENCODE = Encode::Undefined;

    /** @var array<string, string|null> */
    public const PROPERTIES = [];

    /** @var string|null */
    public const NAME = null;
}
