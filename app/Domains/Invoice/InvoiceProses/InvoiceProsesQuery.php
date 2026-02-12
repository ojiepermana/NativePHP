<?php

namespace App\Domains\Invoice\InvoiceProses;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceProsesQuery
{
    public static function query(string $status, InvoiceProsesFilter $filter): array
    {
        $sql = self::buildSql($status, $filter);

        // Async SQL logging (local only)
        if (app()->environment(['local', 'development'])) {
            defer(fn () => Log::channel('daily')->info('InvoiceProsesQuery', [
                'status' => $status,
                'sql' => $sql,
            ]));
        }

        return DB::connection('mysql')->select($sql);
    }

    public static function count(string $status, InvoiceProsesFilter $filter): int
    {
        $sql = self::buildCountSql($status, $filter);

        if (app()->environment(['local', 'development'])) {
            defer(fn () => Log::channel('daily')->info('InvoiceProsesQuery::count', [
                'status' => $status,
                'sql' => $sql,
            ]));
        }

        $result = DB::connection('mysql')->select($sql);

        return $result[0]->total ?? 0;
    }

    private static function buildSql(string $status, InvoiceProsesFilter $filter): string
    {
        $statusCondition = match ($status) {
            'draft' => "e_invoice.status = 'draft'",
            'proses' => "e_invoice.status = 'proses'",
            'selesai' => "e_invoice.status = 'selesai'",
            'laporan' => "e_invoice.status = 'laporan'",
            default => "e_invoice.status = '{$status}'"
        };

        $whereClause = $filter->toSqlFilter();
        $perPage = $filter->getPerPage();
        $offset = $filter->getOffset();

        return "
            SELECT
                e_invoice.id,
                e_invoice.created_at,
                pegawai.nama_depan AS pegawai,
                kantor.nama_kantor AS kantor,
                e_invoice.nomor,
                e_invoice.status,
                e_invoice.e_materai_qty,
                COUNT(e_invoice_item.id) AS qty
            FROM
                erp_invoice.e_invoice
                JOIN erp_hr.pegawai ON (pegawai.id_pegawai = e_invoice.id_pegawai)
                JOIN erp.kantor ON (e_invoice.id_kantor = kantor.id_kantor)
                JOIN erp_invoice.e_invoice_item ON (e_invoice.id = e_invoice_item.id_e_invoice)
            WHERE {$statusCondition}
            {$whereClause}
            GROUP BY
                e_invoice.id
            ORDER BY
                e_invoice.created_at ASC
            LIMIT {$perPage} OFFSET {$offset}
        ";
    }

    private static function buildCountSql(string $status, InvoiceProsesFilter $filter): string
    {
        $statusCondition = match ($status) {
            'draft' => "e_invoice.status = 'draft'",
            'proses' => "e_invoice.status = 'proses'",
            'selesai' => "e_invoice.status = 'selesai'",
            'laporan' => "e_invoice.status = 'laporan'",
            default => "e_invoice.status = '{$status}'"
        };

        $whereClause = $filter->toSqlFilter();

        return "
            SELECT COUNT(DISTINCT e_invoice.id) AS total
            FROM
                erp_invoice.e_invoice
                JOIN erp_hr.pegawai ON (pegawai.id_pegawai = e_invoice.id_pegawai)
                JOIN erp.kantor ON (e_invoice.id_kantor = kantor.id_kantor)
                JOIN erp_invoice.e_invoice_item ON (e_invoice.id = e_invoice_item.id_e_invoice)
            WHERE {$statusCondition}
            {$whereClause}
        ";
    }
}
