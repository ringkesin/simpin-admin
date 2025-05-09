<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\SimulasiPinjamanModels;

class SimulasiPinjamanSeeder extends Seeder
{
     protected $data = [
                            ['p_jenis_pinjaman_id' => 1, 'tenor' => 12, 'margin' => 6.91, 'tahun_margin' => 2025],
                            ['p_jenis_pinjaman_id' => 1, 'tenor' => 24, 'margin' => 6.99, 'tahun_margin' => 2025],
                            ['p_jenis_pinjaman_id' => 1, 'tenor' => 36, 'margin' => 7.13, 'tahun_margin' => 2025],
                            ['p_jenis_pinjaman_id' => 1, 'tenor' => 48, 'margin' => 7.27, 'tahun_margin' => 2025]
    ];

  public function run()
  {
      foreach ($this->data as $d) {
        SimulasiPinjamanModels::create([
                "p_jenis_pinjaman_id" => $d['p_jenis_pinjaman_id'],
                "tenor" => $d['tenor'],
                "margin" => $d['margin'],
                "tahun_margin" => $d['tahun_margin']
          ]);
      }
  }
}
