<?php

namespace App\Livewire\Page\Main\Tabungan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\VTabunganSaldoPerYear;
use App\Traits\MyHelpers;

class TabunganAnggotaSaldoTable extends DataTableComponent
{
    protected $model = VTabunganSaldoPerYear::class;
    public $p_anggota_id;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_tabungan_saldo_id');
        $this->setComponentWrapperAttributes([
            'default' => true,
            'class' => 'rounded-none',
        ]);

        $this->setTableWrapperAttributes([
            'default' => true,
            'class' => 'mt-6 p-0 rounded-none',
        ]);

        $this->setTableAttributes([
            'default' => true,
            'class' => 'table-auto border-y-1 w-full'
        ]);

        $this->setTheadAttributes([
            'default' => true,
            'class' => 'bg-slate-50 dark:bg-slate-900/20 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 uppercase border-y text-xs',
        ]);

        $this->setThAttributes(function (Column $column) {
            return [
                'default' => true,
                'class' => 'py-3 pl-5 px-2',
            ];
        });

        $this->setThSortButtonAttributes(function (Column $column) {
            return [
                'class' => 'font-semibold text-left',
            ];
        });

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setPerPageAccepted([5, 10, 25, 50, 100]);
        $this->setPerPage(5);
        $this->getPerPageDisplayedItemCount();
    }

    public function columns(): array
    {
        return [
            Column::make("Tahun", "tahun")
                ->sortable()
                ->searchable(),
            Column::make("Jenis Tabungan", "nama")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nama', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Total Tahun Ini", "total_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('total', $direction);
                })
                ->searchable()
                ->footer(function($rows) {
                    return 'Total : ' . number_format($rows->sum('total'), 2, ',', '.');
                }),
            Column::make("Total s.d Tahun Ini", "total_sd_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('total_sd', $direction);
                })
                ->searchable()
                ->footer(function($rows) {
                    return 'Total : ' . number_format($rows->sum('total_sd'), 2, ',', '.');
                }),
        ];
    }

    public function builder(): Builder
    {
        return VTabunganSaldoPerYear::query()
            ->where('p_anggota_id', $this->p_anggota_id)
            ->select();
    }
}
