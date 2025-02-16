<?php

namespace App\Traits;

use App\Models\Master\AppsParamModels;

trait MyParams
{
    function getParams($keys = [])
    {
        $params = AppsParamModels::whereIn('param_key', $keys)->get()->toArray();
        $output = [];
        if (count($params) > 0) {
            foreach ($params as $p) {
                $output[$p['param_key']] = $p['param_value'];
            }
        }
        return $output;
    }
}
