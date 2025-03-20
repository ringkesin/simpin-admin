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
            "code" => "web_super_admin",
            "name" => "Web - Super Admin",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 1,
            "code" => "web_admin_simpin",
            "name" => "Web - Admin Simpin",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 2,
            "code" => "mobile_admin",
            "name" => "Mobile - Admin",
            "remarks" => "",
            "valid_from" => "2023-09-01",
            "valid_until" => NULL
        ],
        [
            "apps_id" => 2,
            "code" => "mobile_anggota",
            "name" => "Mobile - Anggota",
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
