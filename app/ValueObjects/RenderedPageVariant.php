<?php

namespace App\ValueObjects;

use App\Models\Page;
use App\Models\PageVariant;

class RenderedPageVariant
{

    use HasInitializationConstructor;

    /**
     * @var PageVariant
     */
    private $pageVariant;

    /**
     * @var string
     */
    private $lead;

    /**
     * @var string
     */
    private $content;

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->pageVariant->page;
    }

    /**
     * @return PageVariant
     */
    public function getPageVariant(): PageVariant
    {
        return $this->pageVariant;
    }

    /**
     * @return string
     */
    public function getLead(): string
    {
        return $this->lead;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

}