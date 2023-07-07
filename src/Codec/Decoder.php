<?php
/*
 * Copyright 2023-present MongoDB, Inc.
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

namespace MongoDB\Codec;

use MongoDB\Exception\InvalidArgumentException;

/**
 * @internal
 * @psalm-template BSONType
 * @psalm-template NativeType
 */
interface Decoder
{
    /**
     * Checks if the decoder supports a given value.
     *
     * @param mixed $value
     * @psalm-assert-if-true BSONType $value
     */
    public function canDecode($value): bool;

    /**
     * Decodes a given value. If the decoder does not support the value, it
     * should throw an exception.
     *
     * @param mixed $value
     * @psalm-param BSONType $value
     * @return mixed
     * @psalm-return NativeType
     * @throws InvalidArgumentException if the decoder does not support the value
     */
    public function decode($value);

    /**
     * Decodes a given value if supported, otherwise returns the value as-is.
     *
     * The DecodeIfSupported trait provides a default implementation of this
     * method.
     *
     * @param mixed $value
     * @psalm-param mixed $value
     * @return mixed
     * @psalm-return ($value is BSONType ? NativeType : $value)
     */
    public function decodeIfSupported($value);
}
