<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\StatusPembayaranModels;

class MasterStatusPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $data = [
        [
            'kode' => 'unpaid',
            'nama' => 'Unpaid',
        ],
        [
            'kode' => 'paid',
            'nama' => 'Paid',
        ]
    ];

    public function run(): void
    {
        foreach ($this->data as $d) {
            StatusPembayaranModels::create([
                "status_code" => $d['kode'],
                "status_name" => $d['nama']
            ]);
        }
    }
}
