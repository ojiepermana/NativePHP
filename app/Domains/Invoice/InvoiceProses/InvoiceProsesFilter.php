<?php

namespace App\Domains\Invoice\InvoiceProses;

use Illuminate\Http\Request;

class InvoiceProsesFilter
{
    public function __construct(
        public ?string $office = null,
        public ?string $search = null,
        public int $page = 1,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            office: $request->input('office'),
            search: $request->input('search'),
            page: max(1, (int) $request->input('page', 1)),
        );
    }

    public function toSqlFilter(): string
    {
        $conditions = [];

        if ($this->office && $this->office !== '') {
            $escaped = addslashes($this->office);
            $conditions[] = "kantor.nama_kantor = '{$escaped}'";
        }

        if ($this->search && $this->search !== '') {
            $escaped = addslashes($this->search);
            $conditions[] = "(
                e_invoice.nomor LIKE '%{$escaped}%'
                OR pegawai.nama_depan LIKE '%{$escaped}%'
                OR CAST(e_invoice.id AS CHAR) LIKE '%{$escaped}%'
            )";
        }

        return empty($conditions) ? '' : 'AND '.implode(' AND ', $conditions);
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * 100;
    }

    public function getPerPage(): int
    {
        return 100;
    }
}
