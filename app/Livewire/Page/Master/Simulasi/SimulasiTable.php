<?php

namespace App\Livewire\Page\Master\Simulasi;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Master\SimulasiPinjamanModels;
use App\Traits\MyHelpers;

class SimulasiTable extends DataTableComponent
{
    protected $model = SimulasiPinjamanModels::class;

    use MyHelpers;

    public function configure(): void
    {
       $this->setPrimaryKey('id')
        ->setTableRowUrl(function($row) {
            return route('master.simulasi.show', ['id' => $row->id]);
        });

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

        $this->setPerPageAccepted([10, 25, 50, 100]);
        $this->getPerPageDisplayedItemCount();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make("Jenis Pinjaman", "jenisPinjaman.nama")
                ->sortable()
                ->searchable(),
            Column::make("Tahun", "tahun_margin")
                ->sortable()
                ->searchable(),
            Column::make("Tenor (Bulan)", "tenor")
                ->sortable()
                ->searchable(),
            Column::make("Margin (%)", "margin")
                ->sortable(),
        ];
    }

    // public function bulkActions(): array
    // {
    //     return [
    //         'activate' => 'Activate',
    //         'deactivate' => 'Deactivate',
    //     ];
    // }
}
