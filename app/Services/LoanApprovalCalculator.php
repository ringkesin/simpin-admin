<?php

namespace App\Services;

class LoanApprovalCalculator
{
    public function calculate(
        float $principal,
        float $marginPercentage,
        float $adminPercentage,
        int $tenor,
        int $loanTypeId,
    ): array {
        $tenor = max($tenor, 1);

        if ($loanTypeId !== 1) {
            $marginAmount = $principal * ($marginPercentage / 100);
            $monthlyInstallment = ($principal / $tenor) + ($marginAmount / $tenor);
        } else {
            $durationInYears = $tenor / 12;
            $marginAmount = $principal * ($marginPercentage / 100) * $durationInYears;
            $adminForInstallment = $principal * ($adminPercentage / 100) * $durationInYears;
            $monthlyInstallment = ($principal + $marginAmount + $adminForInstallment) / $tenor;
        }

        $adminAmount = $principal * ($adminPercentage / 100);
        $totalWithMargin = $principal + $marginAmount;

        return [
            'jumlah_pinjaman_disetujui' => $this->money($principal),
            'tenor_bulan' => $tenor,
            'margin' => [
                'persen' => $marginPercentage,
                ...$this->money($marginAmount),
            ],
            'biaya_admin' => [
                'persen' => $adminPercentage,
                ...$this->money($adminAmount),
            ],
            'total_jumlah_disetujui_dan_margin' => $this->money($totalWithMargin),
            'estimasi_cicilan_per_bulan' => $this->money(round($monthlyInstallment)),
        ];
    }

    private function money(float $amount): array
    {
        $formatted = number_format($amount, 2, ',', '.');

        if (str_ends_with($formatted, ',00')) {
            $formatted = substr($formatted, 0, -3);
        }

        return [
            'nilai' => $amount,
            'rupiah' => 'Rp. '.$formatted,
        ];
    }
}
