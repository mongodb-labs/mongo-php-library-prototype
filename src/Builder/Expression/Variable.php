<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

class Variable implements ExpressionInterface
{
    public const ACCEPTED_TYPES = ['string'];

    public string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }
}
