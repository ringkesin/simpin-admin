<?php

namespace Database\Seeders;

use App\Models\Master\AnggotaModels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MasterAnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $data = [
        [
            'nama' => 'Suyono Sonto S',
            'tanggal_masuk' => '2007-09-13',
            'valid_from' => '2021-02-02',
            'valid_to' => null,
            'nik' => null,
            'alamat' => null,
            'ktp' => null,
            'tempat_lahir' => null,
            'tgl_lahir' => null,
            'p_jenis_kelamin_id' => null,
            'user_id' => null,
            'p_company_id' => null,
            'p_unit_id' => null
        ]
    ];

    public function run(): void
    {
        foreach($this->data as $d) {
            AnggotaModels::create([
                'nama' => $d['nama'],
                'tanggal_masuk' => $d['tanggal_masuk'],
                'valid_from' => $d['valid_from'],
                'valid_to' => $d['valid_to'],
                'valid_to' => $d['valid_to'],

                'nik' => $d['nik'],
                'alamat' => $d['alamat'],
                'ktp' => $d['ktp'],
                'tempat_lahir' => $d['tempat_lahir'],
                'tgl_lahir' => $d['tgl_lahir'],
                'p_jenis_kelamin_id' => $d['p_jenis_kelamin_id'],
                'user_id' => $d['user_id'],
                'p_company_id' => $d['p_company_id'],
                'p_unit_id' => $d['p_unit_id']
            ]);
        }
    }
}
