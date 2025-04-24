<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\JenisTabunganModels;

class MasterJenisTabunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     protected $data = [
        [
            'nama' => 'Simpanan Pokok',
        ],
        [
            'nama' => 'Simpanan Wajib',
        ],
        [
            'nama' => 'Tabungan Sukarela',
        ],
        [
            'nama' => 'Tabungan Indir',
        ],
        [
            'nama' => 'Kompensasi Masa Kerja',
        ]
    ];


    public function run(): void
    {
        foreach ($this->data as $d) {
            JenisTabunganModels::create([
                "nama" => $d['nama']
            ]);
        }
    }
}
