<?php

namespace Database\Seeders;

use App\Models\Rbac\RoleUserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RbacRoleUserSeeder extends Seeder
{
    protected $data = [
        [
            "role_id" => 1,
            "valid_from" => "2023-09-01",
            "items" => [
                [
                    'user_id' => 1
                ],
            ]
        ],
        [
            "role_id" => 3,
            "valid_from" => "2023-09-01",
            "items" => [
                [
                    'user_id' => 1
                ],
            ]
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
            foreach($d['items'] as $childs) {
                RoleUserModel::create(
                    [
                        'role_id' => $d['role_id'],
                        'user_id' => $childs['user_id'],
                        'valid_from' => $d['valid_from']
                    ]
                );
            }
        }
    }
}
