<?php

namespace Tests\Unit;

use App\Models\MemberNotification;
use App\Services\Push\NotificationNavigationResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NotificationNavigationResolverTest extends TestCase
{
    #[DataProvider('targets')]
    public function test_notification_targets_resolve_to_the_expected_scope(
        string $target,
        string $scope,
        array $params,
    ): void {
        $notification = new MemberNotification([
            'type' => 'test',
            'data' => array_merge(['target' => $target], $params),
        ]);

        $navigation = (new NotificationNavigationResolver)->resolve($notification);

        $this->assertTrue($navigation['is_actionable']);
        $this->assertSame('open_screen', $navigation['action']);
        $this->assertSame($scope, $navigation['scope']);
        $this->assertSame($target, $navigation['target']);
        $this->assertSame($params, $navigation['params']);
    }

    public static function targets(): array
    {
        return [
            'profile' => ['profile', 'profile', []],
            'content' => ['content_detail', 'content', ['t_content_id' => '01KCONTENT']],
            'pinjaman' => ['pinjaman_detail', 'pinjaman', ['t_pinjaman_id' => '10']],
            'tagihan' => ['tagihan_detail', 'tagihan', ['t_tagihan_id' => '20']],
            'pencairan' => ['pencairan_tabungan_detail', 'pencairan', ['t_tabungan_pengambilan_id' => '30']],
            'penyertaan' => ['penyertaan_detail', 'penyertaan', ['t_tabungan_penyertaan_id' => '40']],
            'perubahan penyertaan' => ['perubahan_penyertaan_detail', 'perubahan_penyertaan', ['t_tabungan_perubahan_penyertaan_id' => '50']],
            'chat' => ['chat', 'chat', ['t_chat_id' => '60']],
            'mart' => ['order_detail', 'mart', ['cart_mobile_id' => '70']],
        ];
    }

    public function test_missing_detail_identifier_is_not_actionable(): void
    {
        $notification = new MemberNotification([
            'type' => 'pinjaman_approved',
            'data' => ['target' => 'pinjaman_detail'],
        ]);

        $navigation = (new NotificationNavigationResolver)->resolve($notification);

        $this->assertFalse($navigation['is_actionable']);
        $this->assertSame('none', $navigation['action']);
        $this->assertSame(['t_pinjaman_id'], $navigation['missing_params']);
        $this->assertSame('notifications', $navigation['fallback_target']);
    }
}
