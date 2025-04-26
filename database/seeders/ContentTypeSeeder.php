<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Master\ContentTypeModels;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $data = [
        [
            'content_code' => 'berita',
            'content_name' => 'Berita'
        ]
    ];


    public function run(): void
    {
        foreach ($this->data as $d) {
            ContentTypeModels::create([
                "content_code" => $d['content_code'],
                "content_name" => $d['content_name']
            ]);
        }
    }
}
