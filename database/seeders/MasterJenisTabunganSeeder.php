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
            'withdrawal' => 1,
        ],
        [
            'nama' => 'Simpanan Wajib',
            'withdrawal' => 1,
        ],
        [
            'nama' => 'Tabungan Sukarela',
            'withdrawal' => 1,
        ],
        [
            'nama' => 'Tabungan Indir',
            'withdrawal' => 1,
        ],
        [
            'nama' => 'Kompensasi Masa Kerja',
            'withdrawal' => 1,
        ]
    ];


    public function run(): void
    {
        foreach ($this->data as $d) {
            JenisTabunganModels::create([
                "nama" => $d['nama'],
                "withdrawal" => $d['withdrawal'],
            ]);
        }
    }
}
