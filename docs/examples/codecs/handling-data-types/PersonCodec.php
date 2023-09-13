<?php

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

final class PersonCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function __construct(
        private readonly DateTimeCodec $dateTimeCodec = new DateTimeCodec(),
    ) {
    }

    public function canDecode(mixed $value): bool
    {
        return $value instanceof Document && $value->has('name');
    }

    public function canEncode(mixed $value): bool
    {
        return $value instanceof Person;
    }

    public function decode(mixed $value): Person
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return new Person(
            $value->get('name'),
            $this->dateTimeCodec->decode($value->get('createdAt')),
            $value->get('_id'),
        );
    }

    public function encode(mixed $value): Document
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            '_id' => $value->id,
            'name' => $value->name,
            'createdAt' => $this->dateTimeCodec->encode($value->createdAt),
        ]);
    }
}
