<?php

namespace App\Domains\Job\Billing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JobBillingService
{
    public static function getList(string $status, Request $request): array
    {
        $filter = JobBillingFilter::fromRequest($request);
        $cacheKey = self::generateCacheKey($status, $filter);

        return Cache::remember($cacheKey, now()->addSeconds(55), function () use ($status, $filter) {
            $data = JobBillingQuery::query($status, $filter);
            $total = JobBillingQuery::count($status, $filter);

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
        });
    }

    public static function getOffices(): array
    {
        return Cache::remember('job-billing:offices', now()->addMinutes(30), function () {
            $sql = '
                SELECT DISTINCT kantor.nama_kantor
                FROM erp.kantor
                ORDER BY kantor.nama_kantor ASC
            ';

            $result = \Illuminate\Support\Facades\DB::connection('mysql')->select($sql);

            return array_map(fn ($row) => $row->nama_kantor, $result);
        });
    }

    private static function generateCacheKey(string $status, JobBillingFilter $filter): string
    {
        return sprintf(
            'job-billing:%s:office=%s:search=%s:page=%d',
            $status,
            $filter->office ?? 'all',
            md5($filter->search ?? ''),
            $filter->page
        );
    }
}
