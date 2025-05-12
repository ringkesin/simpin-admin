<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\MetodePembayaranModels;

class MasterMetodePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $data = [
        [
            'kode' => 'cash',
            'nama' => 'Cash',
        ],
        [
            'kode' => 'tf',
            'nama' => 'Bank Transfer',
        ],
        [
            'kode' => 'tongji',
            'nama' => 'Potong Gaji',
        ],
        [
            'kode' => 'other',
            'nama' => 'Lainnya'
        ]
    ];

    public function run(): void
    {
        foreach ($this->data as $d) {
            MetodePembayaranModels::create([
                "metode_code" => $d['kode'],
                "metode_name" => $d['nama']
            ]);
        }
    }
}
