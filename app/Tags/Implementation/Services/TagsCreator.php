<?php

namespace App\Tags\Implementation\Services;

use App\Tags\Events\TagCreated;
use App\Tags\Exceptions\TagException;
use App\Tags\Implementation\Repositories\TagsRepositoryInterface;
use App\Tags\Models\Tag;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcherContract;

/**
 * @see \Tests\Unit\Tags\CreateTest
 */
class TagsCreator
{

    /**
     * @var EventsDispatcherContract
     */
    private $eventsDispatcher;

    /**
     * @var TagsRepositoryInterface
     */
    private $tagsRepository;

    /**
     * @var TagsValidator
     */
    private $tagsValidator;

    /**
     * @param EventsDispatcherContract $eventsDispatcher
     * @param TagsRepositoryInterface $tagsRepository
     * @param TagsValidator $tagsValidator
     */
    public function __construct(
        EventsDispatcherContract $eventsDispatcher,
        TagsRepositoryInterface $tagsRepository,
        TagsValidator $tagsValidator
    ) {
        $this->eventsDispatcher = $eventsDispatcher;
        $this->tagsRepository = $tagsRepository;
        $this->tagsValidator = $tagsValidator;
    }

    /**
     * @param array $tagData
     * @return Tag
     *
     * @throws TagException
     */
    public function create(array $tagData): Tag
    {
        $tag = new Tag(
            array_only($tagData, ['language_id', 'name'])
        );

        $this->tagsValidator->validate($tag);
        $this->tagsRepository->persist($tag);

        $this->eventsDispatcher->dispatch(
            new TagCreated($tag)
        );

        return $tag;
    }

}
