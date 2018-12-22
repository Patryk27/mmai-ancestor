<?php

namespace App\Pages\Implementation\Services;

use App\Pages\Exceptions\PageException;
use App\Pages\Implementation\Repositories\PagesRepository;
use App\Pages\Models\Page;
use App\Pages\Queries\GetPagesByIdsQuery;
use App\Pages\Queries\GetPagesByTagIdQuery;
use App\Pages\Queries\PageQuery;
use App\Pages\Queries\SearchPages;
use Illuminate\Support\Collection;

class PagesQuerier
{
    /** @var PagesRepository */
    private $pagesRepository;

    /** @var PagesSearcher */
    private $pagesSearcher;

    public function __construct(
        PagesRepository $pagesRepository,
        PagesSearcher $pagesSearcher
    ) {
        $this->pagesRepository = $pagesRepository;
        $this->pagesSearcher = $pagesSearcher;
    }

    /**
     * @param PageQuery $query
     * @return Collection|Page[]
     * @throws PageException
     */
    public function query(PageQuery $query): Collection
    {
        switch (true) {
            case $query instanceof GetPagesByIdsQuery:
                return $this->pagesRepository->getByIds(
                    $query->getIds()
                );

            case $query instanceof GetPagesByTagIdQuery:
                return $this->pagesRepository->getByTagId(
                    $query->getTagId()
                );

            case $query instanceof SearchPages:
                return $query->applyTo($this->pagesSearcher)->get();

            default:
                throw new PageException(sprintf(
                    'Cannot handle query of class [%s].', get_class($query)
                ));
        }
    }

    /**
     * Returns number of pages matching given query.
     *
     * @param PageQuery $query
     * @return int
     * @throws PageException
     */
    public function count(PageQuery $query): int
    {
        switch (true) {
            case $query instanceof SearchPages:
                return $query->applyTo($this->pagesSearcher)->count();

            default:
                return $this->query($query)->count();
        }
    }
}
