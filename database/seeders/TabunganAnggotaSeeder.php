<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\SimulasiPinjamanModel;

class SimulasiPinjamanSeeder extends Seeder
{
     protected $data = [
                            ['pinjaman' => 5000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 445458, 'tahun_margin' => 2025],
                            ['pinjaman' => 5000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 237458, 'tahun_margin' => 2025],
                            ['pinjaman' => 5000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 168597, 'tahun_margin' => 2025],
                            ['pinjaman' => 5000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 134458, 'tahun_margin' => 2025],

                            ['pinjaman' => 10000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 890917, 'tahun_margin' => 2025],
                            ['pinjaman' => 10000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 474917, 'tahun_margin' => 2025],
                            ['pinjaman' => 10000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 337194, 'tahun_margin' => 2025],
                            ['pinjaman' => 10000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 268917, 'tahun_margin' => 2025],

                            ['pinjaman' => 20000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 1781833, 'tahun_margin' => 2025],
                            ['pinjaman' => 20000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 949833, 'tahun_margin' => 2025],
                            ['pinjaman' => 20000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 674389, 'tahun_margin' => 2025],
                            ['pinjaman' => 20000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 537833, 'tahun_margin' => 2025],

                            ['pinjaman' => 30000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 2672750, 'tahun_margin' => 2025],
                            ['pinjaman' => 30000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 1424750, 'tahun_margin' => 2025],
                            ['pinjaman' => 30000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 1011583, 'tahun_margin' => 2025],
                            ['pinjaman' => 30000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 806750, 'tahun_margin' => 2025],

                            ['pinjaman' => 40000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 3563667, 'tahun_margin' => 2025],
                            ['pinjaman' => 40000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 1899667, 'tahun_margin' => 2025],
                            ['pinjaman' => 40000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 1348778, 'tahun_margin' => 2025],
                            ['pinjaman' => 40000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 1075667, 'tahun_margin' => 2025],

                            ['pinjaman' => 50000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 4454583, 'tahun_margin' => 2025],
                            ['pinjaman' => 50000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 2374583, 'tahun_margin' => 2025],
                            ['pinjaman' => 50000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 1685972, 'tahun_margin' => 2025],
                            ['pinjaman' => 50000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 1344583, 'tahun_margin' => 2025],

                            ['pinjaman' => 100000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 8909167, 'tahun_margin' => 2025],
                            ['pinjaman' => 100000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 4749167, 'tahun_margin' => 2025],
                            ['pinjaman' => 100000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 3371944, 'tahun_margin' => 2025],
                            ['pinjaman' => 100000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 2689167, 'tahun_margin' => 2025],

                            ['pinjaman' => 200000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 17818333, 'tahun_margin' => 2025],
                            ['pinjaman' => 200000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 9498333, 'tahun_margin' => 2025],
                            ['pinjaman' => 200000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 6743889, 'tahun_margin' => 2025],
                            ['pinjaman' => 200000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 5378333, 'tahun_margin' => 2025],

                            ['pinjaman' => 300000000, 'tenor' => 12, 'margin' => 6.91, 'angsuran' => 26727500, 'tahun_margin' => 2025],
                            ['pinjaman' => 300000000, 'tenor' => 24, 'margin' => 6.99, 'angsuran' => 14247500, 'tahun_margin' => 2025],
                            ['pinjaman' => 300000000, 'tenor' => 36, 'margin' => 7.13, 'angsuran' => 10115833, 'tahun_margin' => 2025],
                            ['pinjaman' => 300000000, 'tenor' => 48, 'margin' => 7.27, 'angsuran' => 8067500, 'tahun_margin' => 2025]
    ];

  public function run()
  {
      foreach ($this->data as $d) {
        SimulasiPinjamanModel::create([
              "pinjaman" => $d['pinjaman'],
              "tenor" => $d['tenor'],
              "margin" => $d['margin'],
              "angsuran" => $d['angsuran'],
              "tahun_margin" => $d['tahun_margin']
          ]);
      }
  }
}
