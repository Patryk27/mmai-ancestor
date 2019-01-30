<?php

namespace Tests\Unit\Pages;

use App\Core\Exceptions\Exception as AppException;
use App\Pages\Models\Page;
use App\Tags\Models\Tag;
use Illuminate\Support\Collection;

class CreateTest extends TestCase {

    /**
     * @return void
     * @throws AppException
     */
    public function testFillsBasicProperties(): void {
        $page = $this->pagesFacade->create([
            'language_id' => 100,

            'title' => 'some title',
            'lead' => 'some lead',
            'content' => 'some content',
            'notes' => 'some notes',

            'type' => Page::TYPE_PAGE,
            'status' => Page::STATUS_DRAFT,
        ]);

        $this->assertEquals(100, $page->language_id);
        $this->assertEquals('some title', $page->title);
        $this->assertEquals('some lead', $page->lead);
        $this->assertEquals('some content', $page->content);
        $this->assertEquals('some notes', $page->notes);
        $this->assertEquals(Page::TYPE_PAGE, $page->type);
        $this->assertEquals(Page::STATUS_DRAFT, $page->status);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testCreatesRouteWhenNecessary(): void {
        $page = $this->pagesFacade->create([
            'url' => 'somewhere',
        ]);

        $this->assertNotNull($page->route);
        $this->assertEquals('somewhere', $page->route->url);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testDoesNotCreateRouteWhenUnnecessary(): void {
        $page = $this->pagesFacade->create([
            'language_id' => 100,
        ]);

        $this->assertEquals(100, $page->language_id);
        $this->assertNull($page->route);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testAddsTags(): void {
        /** @var Collection|Tag[] $tags */
        $tags = $this->tagsRepository
            ->getAll()
            ->where('language_id', 100)
            ->values();

        $page = $this->pagesFacade->create([
            'language_id' => 100,
            'tag_ids' => [
                $tags[0]->id,
                $tags[1]->id,
            ],
        ]);

        $this->assertCount(2, $page->tags);
        $this->assertEquals($tags[0], $page->tags[0]);
        $this->assertEquals($tags[1], $page->tags[1]);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testForbidsToAddTagFromOtherLanguage(): void {
        $this->expectExceptionMessage('Page cannot contain tags from other languages.');

        /** @var Collection|Tag[] $tags */
        $tags = $this->tagsRepository
            ->getAll()
            ->where('language_id', 200)
            ->values();

        $this->pagesFacade->create([
            'language_id' => 100,
            'tag_ids' => [
                $tags[0]->id,
            ],
        ]);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testAddsAttachments(): void {
        $attachmentA = $this->createAttachment('attachment-a');
        $attachmentB = $this->createAttachment('attachment-b');

        $page = $this->pagesFacade->create([
            'type' => Page::TYPE_PAGE,
            'status' => Page::STATUS_DRAFT,

            'attachment_ids' => [
                $attachmentA->id,
                $attachmentB->id,
            ],
        ]);

        $this->assertCount(2, $page->attachments);
        $this->assertEquals($attachmentA, $page->attachments[0]);
        $this->assertEquals($attachmentB, $page->attachments[1]);
    }

    /**
     * @return void
     * @throws AppException
     */
    public function testFailsOnNonExistingAttachment(): void {
        $this->expectExceptionMessage('Attachment was not found.');

        $this->pagesFacade->create([
            'type' => Page::TYPE_PAGE,

            'attachment_ids' => [
                100,
            ],
        ]);
    }

}
