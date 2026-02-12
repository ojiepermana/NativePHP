<?php

namespace App\Domains\Billing\Status;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingStatusQuery
{
    public static function query(string $status, BillingStatusFilter $filter): array
    {
        $sql = self::buildSql($status, $filter);

        if (app()->environment(['local', 'development'])) {
            defer(fn () => Log::channel('daily')->info('BillingStatusQuery', [
                'status' => $status,
                'sql' => $sql,
            ]));
        }

        return DB::connection('mysql')->select($sql);
    }

    public static function count(string $status, BillingStatusFilter $filter): int
    {
        $sql = self::buildCountSql($status, $filter);

        $result = DB::connection('mysql')->select($sql);

        return $result[0]->total ?? 0;
    }

    private static function buildSql(string $status, BillingStatusFilter $filter): string
    {
        $escapedStatus = addslashes($status);
        $whereClause = $filter->toSqlFilter();
        $perPage = $filter->getPerPage();
        $offset = $filter->getOffset();

        // Only apply kunjungan > 0 filter for specific statuses, not for 'faktur'
        $kunjunganFilter = in_array($status, ['faktur', 'verifikasi', 'arsip', 'digital']) ? '' : 'AND  tagihan.kunjungan > 0';

        return <<<SQL
            SELECT
                tagihan.id_tagihan,
                tagihan.id_kontrak,
                tagihan.tanggal_tagihan,
                pelanggan.nama AS pelanggan,
                kontrak.no_kontrak,
                kantor.nama_kantor AS kantor,
                tagihan.nilai,
                GROUP_CONCAT(master_syarat_tagihan.nama SEPARATOR ', ') AS dokumen_belum,
                lokasi_tipe.nama segmentasi,
                tagihan.realisasi,
                tagihan.kunjungan
            FROM
                erp_invoice.tagihan
                JOIN erp_pelanggan.kontrak ON (kontrak.id_kontrak = tagihan.id_kontrak)
                JOIN erp_pelanggan.pelanggan ON (pelanggan.id_pelanggan = kontrak.id_pelanggan)
                JOIN erp.kantor ON (kantor.id_kantor = kontrak.id_kantor)
                JOIN erp_pelanggan.lokasi_tipe ON (kontrak.id_lolaksi_tipe=lokasi_tipe.id_lokasi_tipe)
                LEFT JOIN erp_pelanggan.kontrak_syarat_tagihan ON (kontrak_syarat_tagihan.id_kontrak = kontrak.id_kontrak AND kontrak_syarat_tagihan.id_media IS NULL)
                LEFT JOIN erp_pelanggan.master_syarat_tagihan ON (kontrak_syarat_tagihan.id_master_syarat_tagihan = master_syarat_tagihan.id_master_syarat_tagihan AND master_syarat_tagihan.tipe = 'kontrak')
            WHERE
                YEAR(tagihan.tanggal_tagihan) >= 2026
                AND tagihan.status = '{$escapedStatus}'
                AND tagihan.nilai > 0
                {$kunjunganFilter}
                AND tagihan.deleted_at IS NULL
                {$whereClause}
            GROUP BY
                tagihan.id_tagihan,
                tagihan.id_kontrak,
                tagihan.tanggal_tagihan,
                pelanggan.nama,
                kontrak.no_kontrak,
                kantor.nama_kantor,
                tagihan.nilai,
                lokasi_tipe.nama,
                tagihan.realisasi,
                tagihan.kunjungan
            ORDER BY
                tagihan.tanggal_tagihan ASC
            LIMIT {$perPage} OFFSET {$offset}
        SQL;
    }

    private static function buildCountSql(string $status, BillingStatusFilter $filter): string
    {
        $escapedStatus = addslashes($status);
        $whereClause = $filter->toSqlFilter();

        // Only apply kunjungan > 0 filter for specific statuses, not for 'faktur'
        $kunjunganFilter = in_array($status, ['faktur', 'verifikasi', 'arsip', 'digital']) ? '' : 'AND tagihan.kunjungan > 0';

        return <<<SQL
            SELECT
                COUNT(DISTINCT tagihan.id_tagihan) as total
            FROM
                erp_invoice.tagihan
                JOIN erp_pelanggan.kontrak ON (kontrak.id_kontrak = tagihan.id_kontrak)
                JOIN erp_pelanggan.pelanggan ON (pelanggan.id_pelanggan = kontrak.id_pelanggan)
                JOIN erp.kantor ON (kantor.id_kantor = kontrak.id_kantor)
                LEFT JOIN erp_pelanggan.kontrak_syarat_tagihan ON (kontrak_syarat_tagihan.id_kontrak = kontrak.id_kontrak AND kontrak_syarat_tagihan.id_media IS NULL)
                LEFT JOIN erp_pelanggan.master_syarat_tagihan ON (kontrak_syarat_tagihan.id_master_syarat_tagihan = master_syarat_tagihan.id_master_syarat_tagihan AND master_syarat_tagihan.tipe = 'kontrak')
            WHERE
                YEAR(tagihan.tanggal_tagihan) >= 2026
                AND tagihan.status = '{$escapedStatus}'
                AND tagihan.nilai > 0
                {$kunjunganFilter}
                AND tagihan.deleted_at IS NULL
                {$whereClause}
        SQL;
    }
}
