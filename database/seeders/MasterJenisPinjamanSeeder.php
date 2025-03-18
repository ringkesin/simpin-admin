<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\JenisPinjamanModels;

class MasterJenisPinjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $data = [
        [
            'nama' => 'Pinjaman Umum',
        ],
        [
            'nama' => 'Pinjaman Khusus',
        ],
        [
            'nama' => 'Pinjaman Barang',
        ]
    ];


    public function run(): void
    {
        foreach ($this->data as $d) {
            JenisPinjamanModels::create([
                "nama" => $d['nama']
            ]);
        }
    }
}
