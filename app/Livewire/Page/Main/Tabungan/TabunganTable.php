<?php

namespace App\Livewire\Page\Main\Tabungan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\TabunganModels;
use App\Traits\MyHelpers;

class TabunganTable extends DataTableComponent
{
    protected $model = TabunganModels::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_tabungan_id')
        ->setTableRowUrl(function($row) {
            return route('main.tabungan.show', ['id' => $row->t_tabungan_id]);
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
            Column::make("ID", "t_tabungan_id")
                ->sortable()
                ->searchable(),
            Column::make("Nomor Anggota", "masterAnggota.nomor_anggota")
                ->sortable()
                ->searchable(),
            Column::make("Nama", "masterAnggota.nama")
                ->sortable()
                ->searchable(),
            Column::make("Bulan", "bulan")
                ->sortable()
                ->searchable(),
            Column::make("Tahun", "tahun")
                ->sortable()
                ->searchable(),
            Column::make("Simpanan Pokok", "simpanan_pokok")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Simpanan Wajib", "simpanan_wajib")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Tabungan Sukarela", "tabungan_sukarela")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Tabungan Indir", "tabungan_indir")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Kompensasi Masa Kerja", "kompensasi_masa_kerja")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
        ];
    }
}
