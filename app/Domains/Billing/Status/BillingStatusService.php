<?php

namespace App\Domains\Billing\Status;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillingStatusService
{
    public static function getList(string $status, Request $request): array
    {
        $filter = BillingStatusFilter::fromRequest($request);
        $cacheKey = self::generateCacheKey($status, $filter);

        return Cache::remember($cacheKey, now()->addSeconds(55), function () use ($status, $filter) {
            $data = BillingStatusQuery::query($status, $filter);
            $total = BillingStatusQuery::count($status, $filter);

            return [
                'data' => $data,
                'total' => $total,
                'per_page' => $filter->getPerPage(),
                'current_page' => $filter->page,
                'total_pages' => (int) ceil($total / $filter->getPerPage()),
            ];
        });
    }

    public static function getOffices(): array
    {
        return Cache::remember('billing:offices', now()->addMinutes(30), function () {
            $sql = '
                SELECT DISTINCT kantor.nama_kantor
                FROM erp.kantor
                ORDER BY kantor.nama_kantor ASC
            ';

            $result = DB::connection('mysql')->select($sql);

            return array_map(fn ($row) => $row->nama_kantor, $result);
        });
    }

    private static function generateCacheKey(string $status, BillingStatusFilter $filter): string
    {
        return sprintf(
            'billing:status:%s:office:%s:search:%s:page:%d',
            $status,
            $filter->office ?? 'all',
            md5($filter->search ?? ''),
            $filter->page
        );
    }
}
