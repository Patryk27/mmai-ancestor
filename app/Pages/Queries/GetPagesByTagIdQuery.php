<?php

namespace App\Pages\Queries;

final class GetPagesByTagIdQuery implements PageQuery
{
    /*** @var int */
    private $tagId;

    public function __construct(int $tagId)
    {
        $this->tagId = $tagId;
    }

    /**
     * @return int
     */
    public function getTagId(): int
    {
        return $this->tagId;
    }
}