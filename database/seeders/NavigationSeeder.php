<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Navigation::query()->delete();

        // Dashboard
        Navigation::create([
            'label' => 'Dashboard',
            'icon' => 'home',
            'url' => '/dashboard',
            'is_expandable' => false,
            'order' => 1,
        ]);

        // Pekerjaan Group
        $pekerjaan = Navigation::create([
            'label' => 'Pekerjaan',
            'icon' => 'clipboard-document-check',
            'is_expandable' => true,
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $pekerjaan->id,
            'label' => 'Belum',
            'route_name' => 'job.billing',
            'route_params' => ['status' => 'belum'],
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $pekerjaan->id,
            'label' => 'Lengkap',
            'route_name' => 'job.billing',
            'route_params' => ['status' => 'lengkap'],
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $pekerjaan->id,
            'label' => 'Selesai',
            'route_name' => 'job.billing',
            'route_params' => ['status' => 'selesai'],
            'order' => 3,
        ]);

        // Tagihan Group
        $tagihan = Navigation::create([
            'label' => 'Tagihan',
            'icon' => 'currency-dollar',
            'is_expandable' => true,
            'order' => 3,
        ]);

        Navigation::create([
            'parent_id' => $tagihan->id,
            'label' => 'Belum',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'belum'],
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $tagihan->id,
            'label' => 'Bermasalah',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'bermasalah'],
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $tagihan->id,
            'label' => 'Dibatalkan',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'dibatalkan'],
            'order' => 3,
        ]);

        Navigation::create([
            'parent_id' => $tagihan->id,
            'label' => 'Selesai',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'faktur'],
            'order' => 4,
        ]);

        // Dokumen Group
        $dokumen = Navigation::create([
            'label' => 'Dokumen',
            'icon' => 'folder',
            'is_expandable' => true,
            'order' => 4,
        ]);

        Navigation::create([
            'parent_id' => $dokumen->id,
            'label' => 'Verifikasi',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'verifikasi'],
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $dokumen->id,
            'label' => 'Arsip',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'arsip'],
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $dokumen->id,
            'label' => 'Digital',
            'route_name' => 'billing.status',
            'route_params' => ['status' => 'digital'],
            'order' => 3,
        ]);

        // E-Invoice Group
        $einvoice = Navigation::create([
            'label' => 'E-Invoice',
            'icon' => 'paper-airplane',
            'is_expandable' => true,
            'order' => 5,
        ]);

        Navigation::create([
            'parent_id' => $einvoice->id,
            'label' => 'Proses',
            'route_name' => 'invoice.proses',
            'route_params' => ['status' => 'proses'],
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $einvoice->id,
            'label' => 'Selesai',
            'route_name' => 'invoice.proses',
            'route_params' => ['status' => 'selesai'],
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $einvoice->id,
            'label' => 'Laporan',
            'route_name' => 'invoice.proses',
            'route_params' => ['status' => 'laporan'],
            'order' => 3,
        ]);

        // E-Materai Group
        $ematerai = Navigation::create([
            'label' => 'E-Materai',
            'icon' => 'shield-check',
            'is_expandable' => true,
            'order' => 6,
        ]);

        Navigation::create([
            'parent_id' => $ematerai->id,
            'label' => 'Request',
            'url' => '#',
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $ematerai->id,
            'label' => 'Bermasalah',
            'url' => '#',
            'order' => 2,
        ]);

        // Distribusi Group
        $distribusi = Navigation::create([
            'label' => 'Distribusi',
            'icon' => 'truck',
            'is_expandable' => true,
            'order' => 7,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Antrian',
            'url' => '#',
            'order' => 1,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Bermasalah',
            'url' => '#',
            'order' => 2,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Terkirim',
            'url' => '#',
            'order' => 3,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Gagal',
            'url' => '#',
            'order' => 4,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Diterima',
            'url' => '#',
            'order' => 5,
        ]);

        Navigation::create([
            'parent_id' => $distribusi->id,
            'label' => 'Dibuka',
            'url' => '#',
            'order' => 6,
        ]);
    }
}
