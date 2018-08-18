<?php

namespace Tests\Unit\Pages;

use App\Core\Exceptions\Exception as AppException;
use App\Pages\Models\Page;
use App\Pages\Models\PageVariant;
use App\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class UpdateTest extends TestCase
{

    /**
     * @var Page
     */
    private $page;

    /**
     * @var PageVariant
     */
    private $pageVariant;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        /**
         * @var Collection|Tag[] $tags
         */
        $tags = $this->tagsRepository
            ->getAll()
            ->where('language_id', 100)
            ->values();

        // Create an example page variant
        $this->pageVariant = new PageVariant([
            'language_id' => 100,
            'status' => PageVariant::STATUS_DRAFT,
            'title' => 'some title',
            'lead' => 'some lead',
            'content' => 'some content',
        ]);

        $this->pageVariant->setRelations([
            'tags' => new EloquentCollection([
                $tags[0],
            ]),
        ]);

        // Create an example page and bind that page variant to it
        $this->page = new Page([
            'type' => Page::TYPE_CMS,
        ]);

        $this->page->pageVariants->push($this->pageVariant);

        // Save the example page so that we can update it in a second
        $this->pagesRepository->persist($this->page);
    }

    /**
     * This test makes sure that the update() method gracefully fails when it is
     * told to update a page variant with non-existing id.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testFailsOnNonExistingPageVariant(): void
    {
        $this->expectExceptionMessage('was not found inside page');

        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => 123,
                ],
            ],
        ]);
    }

    /**
     * This test makes sure that the update() method properly creates a new
     * page variant, if it is given a page variant without id.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testCreatesNewPageVariant(): void
    {
        // Update page
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'language_id' => 200,
                    'status' => PageVariant::STATUS_DRAFT,
                ]
            ],
        ]);

        // Re-load it
        $this->page = $this->pagesRepository->getById($this->page->id);

        // Make sure update() created a new page variant
        $this->assertCount(2, $this->page->pageVariants);

        // Make sure that newly-created page variant has everything filled correctly
        $pageVariant = $this->page->pageVariants[1];

        $this->assertEquals(200, $pageVariant->language_id);
        $this->assertEquals(PageVariant::STATUS_DRAFT, $pageVariant->status);
    }

    /**
     * This test makes sure that the update() method properly updates basic
     * properties of an already existing page variant.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testUpdatesExistingPageVariant(): void
    {
        // Update page
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'lead' => 'some updated lead',
                ],
            ],
        ]);

        // Re-load it
        $this->page = $this->pagesRepository->getById($this->page->id);

        // Make sure update() did not create any new page variant
        $this->assertCount(1, $this->page->pageVariants);

        // Make sure appropriate values were updated
        $pageVariant = $this->page->pageVariants[0];

        $this->assertEquals(100, $pageVariant->language_id);
        $this->assertEquals('some updated lead', $pageVariant->lead);
    }

    /**
     * This test makes sure that the update() method does not allow to change
     * language of an already existing page variant.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testDoesNotChangeLanguage(): void
    {
        // Update page
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'language_id' => 150,
                ],
            ],
        ]);

        // Re-load it
        $this->page = $this->pagesRepository->getById($this->page->id);

        // Make sure the language has not been changed
        $this->assertCount(1, $this->page->pageVariants);
        $this->assertEquals(100, $this->page->pageVariants[0]->language_id);
    }

    /**
     * This test makes sure that the update() method sets the "published at"
     * to current date & time when page is being published.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testSetsPublishedAt(): void
    {
        // Update page
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => 'somewhere',
                    'status' => PageVariant::STATUS_PUBLISHED,
                ]
            ],
        ]);

        // Re-load it
        $this->page = $this->pagesRepository->getById($this->page->id);

        // Make sure it has been filled the values we provided
        $this->assertNotNull($this->pageVariant->route);
        $this->assertEquals(PageVariant::STATUS_PUBLISHED, $this->pageVariant->status);

        // Make sure update() automatically set the "published at" property
        $this->assertNotNull($this->pageVariant->published_at);
    }

    /**
     * This test makes sure that the update() method creates a new route when
     * told to.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testCreatesRoute(): void
    {
        // Update page
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => 'somewhere',
                ]
            ],
        ]);

        // Re-load it
        $this->page = $this->pagesRepository->getById($this->page->id);

        // Make sure update() created appropriate route
        $this->assertNotNull($this->pageVariant->route);
        $this->assertEquals('somewhere', $this->pageVariant->route->url);
    }

    /**
     * This test makes sure that the update() method updates an already existing
     * route when told to.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testUpdatesRoute(): void
    {
        // Step 1: create a new route (so that we will be able to update it
        // later)
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => 'somewhere',
                ]
            ],
        ]);

        $this->page = $this->pagesRepository->getById($this->page->id);

        // Step 2: remove that route
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => 'somewhere-else',
                ]
            ],
        ]);

        $this->page = $this->pagesRepository->getById($this->page->id);

        $this->assertNotNull($this->pageVariant->route);
        $this->assertEquals('somewhere-else', $this->pageVariant->route->url);
    }

    /**
     * This test makes sure that the update() method removes an already existing
     * route when told to.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testDeletesRoute(): void
    {
        // Step 1: create a new route (so that we will be able to remove it
        // later)
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => 'somewhere',
                ]
            ],
        ]);

        $this->page = $this->pagesRepository->getById($this->page->id);

        // Step 2: remove that route
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'route' => '',
                ]
            ],
        ]);

        $this->page = $this->pagesRepository->getById($this->page->id);

        $this->assertNull($this->pageVariant->route);
    }

    /**
     * This test makes sure it is not possible to publish a page that does not
     * have any route.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testForbidsToPublishPageWithoutRoute(): void
    {
        $this->expectExceptionMessage('Published page must have a route.');

        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'status' => PageVariant::STATUS_PUBLISHED,
                ],
            ],
        ]);
    }

    /**
     * This test makes sure it is not possible not publish a post that does not
     * have any route.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testForbidsToPublishPostWithoutLead(): void
    {
        $this->expectExceptionMessage('Published post must contain a lead.');

        // Step 1: create an example post
        $page = $this->pagesFacade->create([
            'page' => [
                'type' => Page::TYPE_BLOG,
            ],

            'pageVariants' => [
                []
            ],
        ]);

        $pageVariant = $page->pageVariants[0];

        // Step 2: try to publish it
        $this->pagesFacade->update($page, [
            'pageVariants' => [
                [
                    'id' => $pageVariant->id,
                    'route' => 'somewhere',
                    'status' => PageVariant::STATUS_PUBLISHED,
                ]
            ],
        ]);
    }

    /**
     * This test makes sure it is not possible to create a page / post with
     * route located in the "backend" namespace (e.g. "/backend/foo").
     *
     * @return void
     *
     * @throws AppException
     */
    public function testForbidsToCreateRouteInCertainNamespaces(): void
    {
        $this->expectExceptionMessage('It is not possible to create route in the [backend] namespace.');

        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'route' => 'backend/bar',
                ]
            ],
        ]);
    }

    /**
     * This test makes sure that the update() method correctly adds new tags
     * to an already existing page variant.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testAddsTags(): void
    {
        /**
         * @var Collection|Tag[] $tags
         */
        $tags = $this->tagsRepository
            ->getAll()
            ->where('language_id', 100)
            ->values();

        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,

                    'tag_ids' => [
                        $tags[0]->id,
                        $tags[1]->id,
                    ],
                ],
            ],
        ]);

        // Execute the assertions
        $this->assertCount(2, $this->pageVariant->tags);
        $this->assertEquals($tags[0]->id, $this->pageVariant->tags[0]->id);
        $this->assertEquals($tags[0]->id, $this->pageVariant->tags[0]->id);
    }

    /**
     * This test makes sure that the update() method correctly removes existing
     * tags from an already existing page variant.
     *
     * @return void
     *
     * @throws AppException
     */
    public function testRemovesTags(): void
    {
        $this->pagesFacade->update($this->page, [
            'pageVariants' => [
                [
                    'id' => $this->pageVariant->id,
                    'tag_ids' => [],
                ],
            ],
        ]);

        // Execute the assertions
        $this->assertCount(0, $this->pageVariant->tags);
    }

}