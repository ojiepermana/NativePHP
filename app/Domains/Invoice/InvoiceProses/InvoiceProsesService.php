<?php

namespace App\Domains\Invoice\InvoiceProses;

use Illuminate\Http\Request;

class InvoiceProsesService
{
    public static function getList(string $status, Request $request): array
    {
        $filter = InvoiceProsesFilter::fromRequest($request);

        // No cache for list pages as per pages-instructions.md
        $data = InvoiceProsesQuery::query($status, $filter);
        $total = InvoiceProsesQuery::count($status, $filter);

        return [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $data,
            'total' => $total,
            'per_page' => $filter->getPerPage(),
            'current_page' => $filter->page,
            'total_pages' => (int) ceil($total / $filter->getPerPage()),
            'filters' => [
                'office' => $filter->office,
                'search' => $filter->search,
                'page' => $filter->page,
            ],
        ];
    }

    public static function getOffices(): array
    {
        $sql = '
            SELECT DISTINCT kantor.nama_kantor
            FROM erp.kantor
            ORDER BY kantor.nama_kantor ASC
        ';

        $result = \Illuminate\Support\Facades\DB::connection('mysql')->select($sql);

        return array_map(fn ($row) => $row->nama_kantor, $result);
    }
}
