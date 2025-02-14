<?php

namespace App\Traits;

trait MyAlert
{
    public function sweetalert(array $params = [])
    {
        $icon = '';
        $attributes = [];
        if (count($params) > 0) {
            foreach ($params as $k => $p) {
                $attributes[$k] = $p;
                if ($k == 'icon') {
                    $icon = $p;
                }
            }
        }

        $color = '';
        switch ($icon) {
            case 'success':
                $color = '#34d399';
                break;
            case 'info':
                $color = '#2563eb';
                break;
            case 'warning':
                $color = '#eab308';
                break;
            case 'error':
                $color = '#ef4444';
                break;
        }

        $redirectUrl = '';
        if(!empty($params['redirectUrl'])) {
            $redirectUrl = $params['redirectUrl'];
        }

        $showCancelButton = false;
        if($params['showCancelButton']) {
            $showCancelButton = true;
        }

        $attributes['confirmButtonColor'] = $color;
        $attributes['iconColor'] = $color;
        $attributes['cancelButtonColor'] = '#1f2937'; //6b7280
        $attributes['customClass'] = [
            'htmlContainer' => '!text-lg !font-medium !text-slate-800',
            // 'confirmButton' => 'py-3 px-6 mb-4 text-sm font-medium rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none text-white'
        ];
        $attributes['showCancelButton'] = $showCancelButton;
        $attributes['redirectUrl'] = $redirectUrl;
        // dd($attributes);

        $this->dispatch('swal:modal', $attributes);
    }
}