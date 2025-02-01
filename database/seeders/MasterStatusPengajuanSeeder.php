<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterStatusPengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $data = [
        [
            'nama' => 'Pending',
        ],
        [
            'nama' => 'Reject',
        ],
        [
            'nama' => 'Approve',
        ]
    ];


    public function run(): void
    {
        //
    }
}
