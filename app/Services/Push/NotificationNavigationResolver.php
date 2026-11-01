<?php

namespace App\Services\Push;

use App\Models\MemberNotification;

class NotificationNavigationResolver
{
    /**
     * @var array<string, array{scope:string,required:list<string>}>
     */
    private const TARGETS = [
        'home' => ['scope' => 'home', 'required' => []],
        'profile' => ['scope' => 'profile', 'required' => []],
        'content' => ['scope' => 'content', 'required' => []],
        'content_detail' => ['scope' => 'content', 'required' => ['t_content_id']],
        'pinjaman' => ['scope' => 'pinjaman', 'required' => []],
        'pinjaman_detail' => ['scope' => 'pinjaman', 'required' => ['t_pinjaman_id']],
        'tagihan' => ['scope' => 'tagihan', 'required' => []],
        'tagihan_detail' => ['scope' => 'tagihan', 'required' => ['t_tagihan_id']],
        'pencairan_tabungan' => ['scope' => 'pencairan', 'required' => []],
        'pencairan_tabungan_detail' => ['scope' => 'pencairan', 'required' => ['t_tabungan_pengambilan_id']],
        'penyertaan' => ['scope' => 'penyertaan', 'required' => []],
        'penyertaan_detail' => ['scope' => 'penyertaan', 'required' => ['t_tabungan_penyertaan_id']],
        'perubahan_penyertaan' => ['scope' => 'perubahan_penyertaan', 'required' => []],
        'perubahan_penyertaan_detail' => ['scope' => 'perubahan_penyertaan', 'required' => ['t_tabungan_perubahan_penyertaan_id']],
        'chat' => ['scope' => 'chat', 'required' => ['t_chat_id']],
        'order_detail' => ['scope' => 'mart', 'required' => ['cart_mobile_id']],
    ];

    /**
     * @var array<string, string>
     */
    private const TYPE_TARGETS = [
        'anggota_approved' => 'profile',
        'content_published' => 'content_detail',
        'pinjaman_approved' => 'pinjaman_detail',
        'tagihan' => 'tagihan',
        'tagihan_created' => 'tagihan',
        'tagihan_due' => 'tagihan',
        'pencairan_tabungan_approved' => 'pencairan_tabungan_detail',
        'penyertaan_approved' => 'penyertaan_detail',
        'perubahan_penyertaan_approved' => 'perubahan_penyertaan_detail',
        'chat_message' => 'chat',
        'order_status' => 'order_detail',
    ];

    /**
     * @return array{action:string,is_actionable:bool,scope:string,target:?string,params:array<string,mixed>,missing_params:list<string>,fallback_target:string}
     */
    public function resolve(MemberNotification $notification): array
    {
        $data = $notification->data ?? [];
        $target = $data['target'] ?? self::TYPE_TARGETS[$notification->type] ?? null;
        $target = is_scalar($target) ? (string) $target : null;
        $definition = $target !== null ? (self::TARGETS[$target] ?? null) : null;
        $params = collect($data)->except('target')->all();
        $missingParams = $definition === null
            ? []
            : collect($definition['required'])
                ->reject(fn (string $key): bool => isset($params[$key]) && $params[$key] !== '')
                ->values()
                ->all();
        $isActionable = $definition !== null && $missingParams === [];

        return [
            'action' => $isActionable ? 'open_screen' : 'none',
            'is_actionable' => $isActionable,
            'scope' => $definition['scope'] ?? 'general',
            'target' => $target,
            'params' => $params,
            'missing_params' => $missingParams,
            'fallback_target' => 'notifications',
        ];
    }
}
