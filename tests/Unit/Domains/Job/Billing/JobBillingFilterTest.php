<?php

use App\Domains\Job\Billing\JobBillingFilter;
use Illuminate\Http\Request;

describe('JobBillingFilter', function () {
    it('creates filter from request', function () {
        $request = new Request([
            'office' => 'JAKARTA',
            'search' => 'KTR-001',
            'page' => '2',
        ]);

        $filter = JobBillingFilter::fromRequest($request);

        expect($filter->office)->toBe('JAKARTA')
            ->and($filter->search)->toBe('KTR-001')
            ->and($filter->page)->toBe(2);
    });

    it('generates SQL for office filter', function () {
        $filter = new JobBillingFilter(office: 'JAKARTA');
        $sql = $filter->toSqlFilter();

        expect($sql)->toContain("kantor.nama_kantor = 'JAKARTA'");
    });

    it('generates SQL for search filter', function () {
        $filter = new JobBillingFilter(search: 'KTR-001');
        $sql = $filter->toSqlFilter();

        expect($sql)->toContain("LIKE '%KTR-001%'")
            ->and($sql)->toContain('kontrak.no_kontrak')
            ->and($sql)->toContain('pelanggan.nama');
    });

    it('combines multiple filters with AND', function () {
        $filter = new JobBillingFilter(office: 'JAKARTA', search: 'TEST');
        $sql = $filter->toSqlFilter();

        expect($sql)->toContain('AND')
            ->and($sql)->toContain("kantor.nama_kantor = 'JAKARTA'")
            ->and($sql)->toContain("LIKE '%TEST%'");
    });

    it('returns empty string when no filters', function () {
        $filter = new JobBillingFilter;
        expect($filter->toSqlFilter())->toBe('');
    });

    it('escapes SQL injection in office filter', function () {
        $filter = new JobBillingFilter(office: "'; DROP TABLE;--");
        $sql = $filter->toSqlFilter();

        // Check that single quotes are escaped
        expect($sql)->toContain("\\'");
    });

    it('escapes SQL injection in search filter', function () {
        $filter = new JobBillingFilter(search: "'; DROP TABLE;--");
        $sql = $filter->toSqlFilter();

        // Check that single quotes are escaped
        expect($sql)->toContain("\\'");
    });

    it('calculates offset correctly for pagination', function () {
        $filter1 = new JobBillingFilter(page: 1);
        $filter2 = new JobBillingFilter(page: 2);
        $filter3 = new JobBillingFilter(page: 5);

        expect($filter1->getOffset())->toBe(0)
            ->and($filter2->getOffset())->toBe(100)
            ->and($filter3->getOffset())->toBe(400);
    });

    it('returns 100 as per page limit', function () {
        $filter = new JobBillingFilter;
        expect($filter->getPerPage())->toBe(100);
    });

    it('handles page number less than 1', function () {
        $request = new Request(['page' => '0']);
        $filter = JobBillingFilter::fromRequest($request);

        expect($filter->page)->toBe(1);
    });

    it('handles invalid page number', function () {
        $request = new Request(['page' => 'invalid']);
        $filter = JobBillingFilter::fromRequest($request);

        expect($filter->page)->toBe(1);
    });

    it('ignores empty office filter', function () {
        $filter = new JobBillingFilter(office: '');
        expect($filter->toSqlFilter())->toBe('');
    });

    it('ignores empty search filter', function () {
        $filter = new JobBillingFilter(search: '');
        expect($filter->toSqlFilter())->toBe('');
    });
});
