<?php

namespace Database\Seeders;

use App\Models\Master\AppsParamModels;
use Illuminate\Database\Seeder;

class AppsParamSeeder extends Seeder
{
    protected $data = [
        [
            "apps_id" => 1,
            "param_key" => "users_form_avatar_maxsize",
            "param_value" => "2048",
            "data_type" => "string"
        ],
        [
            "apps_id" => 1,
            "param_key" => "users_form_avatar_allowed_filetype",
            "param_value" => "mimetypes:image/jpeg,image/png",
            "data_type" => "string"
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $d) {
            AppsParamModels::create([
                "apps_id" => $d['apps_id'],
                "param_key" => $d['param_key'],
                "param_value" => $d['param_value'],
                "data_type" => $d['data_type']
            ]);
        }
    }
}
