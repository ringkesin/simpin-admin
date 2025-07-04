<?php

namespace App\Livewire\Page\Master\Anggota;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Master\AnggotaModels;

class AnggotaTable extends DataTableComponent
{
    protected $model = AnggotaModels::class;

    public function configure(): void
    {
        $this->setPrimaryKey('p_anggota_id')
        ->setTableRowUrl(function($row) {
            return route('master.anggota.show', ['id' => $row->p_anggota_id]);
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
            Column::make("ID", "p_anggota_id")
                ->sortable()
                ->searchable(),
            Column::make("Nomor Anggota", "nomor_anggota")
                ->sortable()
                ->searchable(),
            Column::make("Nama", "nama")
                ->sortable()
                ->searchable(),
            Column::make("Valid Dari", "valid_from")
                ->sortable(),
            Column::make("Valid Sampai", "valid_to")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return empty($value) ? '-' : $value;
                })->html(),
            Column::make("Anggota ?", "is_registered")
                ->sortable()
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return empty($value) ? '<span class="text-xs font-semibold text-white p-1.5 bg-blue-500 rounded-xl">Belum</span>' :  '<span class="text-xs font-semibold text-white p-1.5 bg-green-500 rounded-xl">Sudah</span>';
                })->html(),
            Column::make("Terdaftar di User", "user_id")
                ->sortable()
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return empty($value) ? '<span class="text-xs font-semibold text-white p-1.5 bg-blue-500 rounded-xl">Belum</span>' :  '<span class="text-xs font-semibold text-white p-1.5 bg-green-500 rounded-xl">Sudah</span>';
                })->html(),
        ];
    }

    public function builder(): Builder
    {
        return AnggotaModels::query()
            ->orderBy('nomor_anggota', 'asc');
    }

    // public function bulkActions(): array
    // {
    //     return [
    //         'activate' => 'Activate',
    //         'deactivate' => 'Deactivate',
    //     ];
    // }
}
