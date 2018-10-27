<?php

namespace App\Analytics\Implementation\Services\Searcher;

use App\Analytics\Implementation\Services\RequestEventsSearcher;
use App\Analytics\Models\Event;
use App\Analytics\Queries\SearchRequestEventsQuery;
use App\Core\Searcher\AbstractEloquentSearcher;
use App\Core\Searcher\EloquentSearcher\EloquentMapper;

class EloquentRequestEventsSearcher extends AbstractEloquentSearcher implements RequestEventsSearcher
{

    private const FIELDS = [
        SearchRequestEventsQuery::FIELD_ID => [
            'column' => 'events.id',
            'type' => EloquentMapper::FIELD_TYPE_NUMBER,
        ],

        SearchRequestEventsQuery::FIELD_TYPE => [
            'column' => 'events.type',
            'type' => EloquentMapper::FIELD_TYPE_ENUM,
        ],

        SearchRequestEventsQuery::FIELD_CREATED_AT => [
            'column' => 'events.created_at',
            'type' => EloquentMapper::FIELD_TYPE_DATETIME,
        ],

        SearchRequestEventsQuery::FIELD_URL => [
            'column' => 'json_value(payload, "$.url")',
            'type' => EloquentMapper::FIELD_TYPE_STRING,
        ],
    ];

    /**
     * @param Event $event
     */
    public function __construct(
        Event $event
    ) {
        parent::__construct(
            $event,
            self::FIELDS
        );
    }

}