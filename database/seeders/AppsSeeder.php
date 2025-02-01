<?php

namespace Database\Seeders;

use App\Models\Rbac\AppsModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $data = [
        [
            'name' => 'Admin Simpan Pinjam',
            'remarks' => 'Aplikasi Admin Simpan Pinjam KKBA',
            'valid_from' => '2023-08-30'
        ]
    ];

    public function run()
    {
        foreach($this->data as $d) {
            if($d['name'] == 'Admin Simpan Pinjam') {
                $kode = '20250122114501';
            } else {
                $kode = now();
            }
            AppsModel::create([
                'code' => 'AP'.$kode,
                'name' => $d['name'],
                'remarks' => (isset($d['remarks']) ? $d['remarks'] : null),
                'valid_from' => $d['valid_from']
            ]);
        }
    }
}
