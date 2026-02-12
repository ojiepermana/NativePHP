<?php

declare(strict_types=1);

use App\Domains\Shared\Document\DocumentService;

if (! function_exists('document_url')) {
    /**
     * Get document URL with fallback.
     */
    function document_url(?string $documentId, ?string $fallback = null): ?string
    {
        return app(DocumentService::class)->getDocumentUrl($documentId, $fallback);
    }
}

if (! function_exists('employee_avatar')) {
    /**
     * Get employee avatar URL with pravatar.cc fallback.
     */
    function employee_avatar(?string $documentId, int $index = 0): string
    {
        return app(DocumentService::class)->getEmployeeAvatarUrl($documentId, $index);
    }
}

if (! function_exists('document_signed_url')) {
    /**
     * Get temporary signed URL for document.
     */
    function document_signed_url(string $documentId, int $minutes = 60): ?string
    {
        return app(DocumentService::class)->getSignedUrl($documentId, $minutes);
    }
}
