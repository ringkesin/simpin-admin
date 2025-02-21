<?php

namespace Database\Seeders;

use App\Models\Rbac\RoleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RbacRoleSeeder extends Seeder
{
    protected $data = [
        [
            "apps_id" => 1,
            "code" => "RA0120240214",
            "name" => "Administrator",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 1,
            "code" => "RA0220240214",
            "name" => "Admin SIMPIN",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 2,
            "code" => "RM0120240214",
            "name" => "Administrator Mobile",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 2,
            "code" => "RM0220240214",
            "name" => "Admin Mobile SIMPIN",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 2,
            "code" => "RM0320240214",
            "name" => "Anggota",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->data as $d) {
            RoleModel::create([
                "apps_id" => $d['apps_id'],
                "code" => $d['code'],
                "name" => $d['name'],
                "remarks" => $d['remarks'],
                "valid_from" => $d['valid_from'],
                "valid_until" => $d['valid_until']
            ]);
        }
    }
}
