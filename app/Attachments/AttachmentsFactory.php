<?php

namespace App\Attachments;

use App\Attachments\Implementation\Repositories\AttachmentsRepositoryInterface;
use App\Attachments\Implementation\Services\AttachmentsCreator;
use App\Attachments\Implementation\Services\AttachmentsDeleter;
use App\Attachments\Implementation\Services\AttachmentsGarbageCollector;
use App\Attachments\Implementation\Services\AttachmentsQuerier;
use App\Attachments\Implementation\Services\AttachmentsStreamer;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

final class AttachmentsFactory
{

    /**
     * Builds an instance of @see AttachmentsFacade.
     *
     * @param FilesystemContract $attachmentsFs
     * @param AttachmentsRepositoryInterface $attachmentsRepository
     * @return AttachmentsFacade
     */
    public static function build(
        FilesystemContract $attachmentsFs,
        AttachmentsRepositoryInterface $attachmentsRepository
    ): AttachmentsFacade {
        $attachmentsCreator = new AttachmentsCreator($attachmentsFs, $attachmentsRepository);
        $attachmentsDeleter = new AttachmentsDeleter($attachmentsFs, $attachmentsRepository);
        $attachmentsGarbageCollector = new AttachmentsGarbageCollector($attachmentsFs, $attachmentsRepository, $attachmentsDeleter);
        $attachmentsPathGenerator = new AttachmentsStreamer($attachmentsFs);
        $attachmentsQuerier = new AttachmentsQuerier($attachmentsRepository);

        return new AttachmentsFacade(
            $attachmentsCreator,
            $attachmentsGarbageCollector,
            $attachmentsPathGenerator,
            $attachmentsQuerier
        );
    }

}
