<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
use stdClass;

/**
 * Groups documents into windows and applies one or more operators to the documents in each window.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
class SetWindowFieldsStage implements StageInterface
{
    public const NAME = '$setWindowFields';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ExpressionInterface|mixed $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection. */
    public mixed $partitionBy;

    /** @param array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting. */
    public stdClass|array $sortBy;

    /**
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     */
    public Document|Serializable|stdClass|array $output;

    /** @param Optional|array|stdClass $window Specifies the window boundaries and parameters. Window boundaries are inclusive. Default is an unbounded window, which includes all documents in the partition. */
    public Optional|stdClass|array $window;

    /**
     * @param ExpressionInterface|mixed $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection.
     * @param array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting.
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     * @param Optional|array|stdClass $window Specifies the window boundaries and parameters. Window boundaries are inclusive. Default is an unbounded window, which includes all documents in the partition.
     */
    public function __construct(
        mixed $partitionBy,
        stdClass|array $sortBy,
        Document|Serializable|stdClass|array $output,
        Optional|stdClass|array $window = Optional::Undefined,
    ) {
        $this->partitionBy = $partitionBy;
        $this->sortBy = $sortBy;
        $this->output = $output;
        $this->window = $window;
    }
}