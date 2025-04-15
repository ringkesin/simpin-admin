<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\StatusPengajuanModels;

class MasterStatusPengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $data = [
        [
            'nama' => 'Draft',
        ],
        [
            'nama' => 'Cancelled',
        ],
        [
            'nama' => 'Pending',
        ],
        [
            'nama' => 'Reject',
        ],
        [
            'nama' => 'Under Review',
        ],
        [
            'nama' => 'Approve',
        ],
        [
            'nama' => 'On installment',
        ],
        [
            'nama' => 'Overdue',
        ],
        [
            'nama' => 'Paid off'
        ]
    ];


    public function run(): void
    {
        foreach ($this->data as $d) {
            StatusPengajuanModels::create([
                "nama" => $d['nama']
            ]);
        }
    }
}
