<?php

declare(strict_types=1);

namespace App\Domains\Shared\Document;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $connection = 'mysql';

    protected $table = 'erp_document.document';

    protected $primaryKey = 'id_document';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_document',
        'for_module',
        'for_table',
        'for_module_id',
        'name',
        'revisi',
        'nomor',
        'tanggal',
        'id_user',
        'file_name',
        'file_ext',
        'file_location',
        'enable_download',
    ];

    public $timestamps = true;

    /**
     * Get the Google Cloud Storage path for this document file.
     */
    public function getGcsPath(): string
    {
        return "{$this->for_module}/{$this->for_module_id}/{$this->file_name}";
    }

    /**
     * Check if this document is an image type based on file extension.
     */
    public function isImage(): bool
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return in_array(strtolower($extension), [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
        ]);
    }

    /**
     * Check if this document is a PDF type.
     */
    public function isPdf(): bool
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        return $extension === 'pdf';
    }

    /**
     * Check if this document is a document type based on file extension.
     */
    public function isDocument(): bool
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        return in_array($extension, [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
        ]);
    }
}
