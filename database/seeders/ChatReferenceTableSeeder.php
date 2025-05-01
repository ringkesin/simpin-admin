<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\ChatReferenceTableModels;

class ChatReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $data = [
        [
            'reference_table_name' => 't_pinjaman',
            'custom_name' => 'Pinjaman',
            'reference_table_key_name' => 't_pinjaman_id'
        ],
        [
            'reference_table_name' => 't_tabungan_pencairan',
            'custom_name' => 'Pencairan Tabungan',
            'reference_table_key_name' => 't_tabungan_pencairan_id'
        ],
    ];

    public function run(): void
    {
        foreach ($this->data as $d) {
            ChatReferenceTableModels::create([
                "reference_table_name" => $d['reference_table_name'],
                "custom_name" => $d['custom_name'],
                "reference_table_key_name" => $d['reference_table_key_name']
            ]);
        }
    }
}
