<?php

declare(strict_types=1);

namespace App\Domains\Shared\Document;

use App\Services\GoogleCloudStorageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DocumentService
{
    public function __construct(
        private GoogleCloudStorageService $gcsService
    ) {}

    /**
     * Get document URL with 24-hour cache.
     *
     * @param  string|null  $moduleId  The for_module_id (e.g., id_pegawai) to search for
     */
    public function getDocumentUrl(?string $moduleId, ?string $fallback = null): ?string
    {
        if (empty($moduleId)) {
            return $fallback;
        }

        $cacheKey = "document_url:{$moduleId}";

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($moduleId, $fallback) {
            $document = Document::where('for_module_id', $moduleId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $document) {
                return $fallback;
            }

            $gcsPath = $document->getGcsPath();

            // Check if file exists in GCS
            if (! $this->gcsService->exists($gcsPath)) {
                return $fallback;
            }

            return $this->gcsService->getFileUrl($gcsPath);
        });
    }

    /**
     * Batch retrieve document URLs with caching.
     *
     * @param  array  $moduleIds  Array of for_module_id values (e.g., id_pegawai)
     */
    public function batchGetDocumentUrls(array $moduleIds, ?string $fallback = null): array
    {
        $urls = [];
        $uncachedIds = [];

        // Check cache first
        foreach ($moduleIds as $moduleId) {
            if (empty($moduleId)) {
                $urls[$moduleId] = $fallback;

                continue;
            }

            $cacheKey = "document_url:{$moduleId}";
            $cachedUrl = Cache::get($cacheKey);

            if ($cachedUrl !== null) {
                $urls[$moduleId] = $cachedUrl;
            } else {
                $uncachedIds[] = $moduleId;
            }
        }

        // Query database for uncached IDs
        if (! empty($uncachedIds)) {
            // Get latest document for each for_module_id
            $documentCollection = Document::whereIn('for_module_id', $uncachedIds)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('for_module_id')
                ->map(fn ($group) => $group->first());

            foreach ($uncachedIds as $moduleId) {
                $document = $documentCollection->get($moduleId);

                if (! $document) {
                    $urls[$moduleId] = $fallback;
                    Cache::put("document_url:{$moduleId}", $fallback, 60 * 60 * 24);

                    continue;
                }

                $gcsPath = $document->getGcsPath();

                // Check if file exists in GCS
                if (! $this->gcsService->exists($gcsPath)) {
                    $urls[$moduleId] = $fallback;
                    Cache::put("document_url:{$moduleId}", $fallback, 60 * 60 * 24);

                    continue;
                }

                $url = $this->gcsService->getFileUrl($gcsPath);
                $urls[$moduleId] = $url;
                Cache::put("document_url:{$moduleId}", $url, 60 * 60 * 24);
            }
        }

        return $urls;
    }

    /**
     * Get employee avatar URL with etos.co.id fallback.
     */
    public function getEmployeeAvatarUrl(?string $documentId, int $index = 0): string
    {
        $fallback = 'https://etos.co.id/assets/moggy-faicon.png';

        return $this->getDocumentUrl($documentId, $fallback) ?? $fallback;
    }

    /**
     * Get signed URL for private files (temporary access).
     *
     * @param  string  $moduleId  The for_module_id (e.g., id_pegawai)
     */
    public function getSignedUrl(string $moduleId, int $minutes = 60): ?string
    {
        $document = Document::where('for_module_id', $moduleId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $document) {
            return null;
        }

        $gcsPath = $document->getGcsPath();

        // Note: league/flysystem-google-cloud-storage doesn't support signed URLs directly
        // You would need to use Google Cloud Storage SDK directly
        // For now, return regular URL
        return $this->gcsService->getFileUrl($gcsPath);
    }

    /**
     * Clear cache for specific document.
     */
    public function clearDocumentCache(string $documentId): void
    {
        Cache::forget("document_url:{$documentId}");
    }

    /**
     * Clear cache for multiple document IDs.
     */
    public function clearMultipleDocumentCache(array $documentIds): void
    {
        foreach ($documentIds as $documentId) {
            $this->clearDocumentCache($documentId);
        }
    }

    /**
     * Get Document model instance.
     *
     * @param  string  $moduleId  The for_module_id (e.g., id_pegawai)
     */
    public function getDocumentInfo(string $moduleId): ?Document
    {
        return Document::where('for_module_id', $moduleId)->first();
    }

    /**
     * Batch get Document model instances.
     *
     * @param  array  $moduleIds  Array of for_module_id values
     */
    public function batchGetDocumentInfo(array $moduleIds): Collection
    {
        return Document::whereIn('for_module_id', $moduleIds)->get()->keyBy('for_module_id');
    }

    /**
     * Download document to local storage.
     *
     * @param  string  $moduleId  The for_module_id (e.g., id_pegawai)
     */
    public function downloadDocument(string $moduleId, string $localPath): bool
    {
        $document = Document::where('for_module_id', $moduleId)->first();

        if (! $document) {
            return false;
        }

        $gcsPath = $document->getGcsPath();

        return $this->gcsService->downloadFile($gcsPath, $localPath);
    }
}
