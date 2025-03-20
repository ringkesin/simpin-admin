<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Master\UnitModels;

class MasterUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $data = [
        [
            'unit_name' => 'Departemen Pemasaran',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Departement Human Capital & General Affairs',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Departemen Keuangan',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Sekretariat Perusahaan',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Divisi Operasi 1',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Divisi Operasi 2',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Divisi Operasi 3',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Divisi Peralatan dan Precast',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Departemen Produksi dan SCM',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Departemen QHSSE',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Satuan Pengawasan Intern',
            'is_project' => false,
        ],
        [
            'unit_name' => 'Pembangunan Jaringan Perpipaan Transmisi Air Minum SPAM Sepaku Paket 1',
            'is_project' => true,
        ],
        [
            'unit_name' => 'Pembangunan Bendungan Bulango Ulu Paket II (MYC) Di Kab. Bone Bolango',
            'is_project' => true,
        ],
        [
            'unit_name' => 'Pembangunan Bangunan dan Pondasi Rumah Sakit Abdi Waluyo Nusantara',
            'is_project' => true,
        ],
        [
            'unit_name' => 'Perbaikan Pasca Bencana Longsor PLTM Padang Guci 2',
            'is_project' => true,
        ],
        [
            'unit_name' => 'Proyek Paket Pekerjaan Konstruksi Terintegrasi Rancang dan Bangun Penataan Sumbu Kebangsaan Tahap I',
            'is_project' => true,
        ],
    ];


    public function run(): void
    {
        $no = 0;
        foreach ($this->data as $d) {
            $no++;
            UnitModels::create([
                "unit_name" => $d['unit_name'],
                "kode_unit" => $no,
                "is_project" => $d['is_project']
            ]);
        }
    }
}
