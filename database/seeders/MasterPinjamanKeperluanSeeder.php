<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\PinjamanKeperluanModels;

class MasterPinjamanKeperluanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $data = [
        [
            'keperluan' => 'Pendidikan',
        ],
        [
            'keperluan' => 'Renovasi',
        ],
        [
            'keperluan' => 'Pembangunan',
        ],
        [
            'keperluan' => 'Kendaraan',
        ],
        [
            'keperluan' => 'Investasi',
        ],
        [
            'keperluan' => 'Pernikahan',
        ],
        [
            'keperluan' => 'Khitanan',
        ],
        [
            'keperluan' => 'Modal Usaha',
        ],
        [
            'keperluan' => 'Lainnya',
        ],
    ];


    public function run(): void
    {
        $no = 0;
        foreach ($this->data as $d) {
            $no++;
            PinjamanKeperluanModels::create([
                "keperluan" => $d['keperluan'],
            ]);
        }
    }
}
