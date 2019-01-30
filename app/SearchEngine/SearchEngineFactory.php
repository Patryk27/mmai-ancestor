<?php

namespace App\SearchEngine;

use App\Pages\PagesFacade;
use App\SearchEngine\Implementation\Policies\PagesIndexerPolicy;
use App\SearchEngine\Implementation\Services\ElasticsearchMigrator;
use App\SearchEngine\Implementation\Services\PagesIndexer;
use App\SearchEngine\Implementation\Services\PagesSearcher;
use Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

final class SearchEngineFactory {

    public static function build(
        EventsDispatcher $eventsDispatcher,
        ElasticsearchClient $elasticsearch,
        PagesFacade $pagesFacade
    ): SearchEngineFacade {
        $elasticsearchMigrator = new ElasticsearchMigrator($elasticsearch);

        $pagesIndexerPolicy = new PagesIndexerPolicy();
        $pagesIndexer = new PagesIndexer($elasticsearch, $pagesIndexerPolicy);

        $pagesSearcher = new PagesSearcher($eventsDispatcher, $elasticsearch, $pagesFacade);

        return new SearchEngineFacade(
            $elasticsearchMigrator,
            $pagesIndexer,
            $pagesSearcher
        );
    }

}
