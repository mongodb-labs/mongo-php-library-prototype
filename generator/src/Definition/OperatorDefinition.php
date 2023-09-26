<?php
declare(strict_types=1);

namespace MongoDB\CodeGenerator\Definition;

use function array_map;

final readonly class OperatorDefinition
{
    /** @var list<ArgumentDefinition> */
    public array $arguments;

    public function __construct(
        public string $name,
        public ?string $type = null,
        public bool $usesNamedArgs = false,
        array $args = [],
    ) {
        $this->arguments = array_map(
            fn ($arg): ArgumentDefinition => new ArgumentDefinition(...$arg),
            $args,
        );
    }
}
