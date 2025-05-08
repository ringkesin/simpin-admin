<?php

namespace App\Livewire\Page\Main\Tabungan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\VTabunganSaldo;
use App\Traits\MyHelpers;

class TabunganTable extends DataTableComponent
{
    protected $model = VTabunganSaldo::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('p_anggota_id')
        ->setTableRowUrl(function($row) {
            return route('main.tabungan.update', ['id' => $row->p_anggota_id]);
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
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nama', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("NIK", "nik")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nik', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Total Tabungan", "total_tabungan_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('total_tabungan', $direction);
                })
                ->searchable(),
        ];
    }

    // public function bulkActions(): array
    // {
    //     return [
    //         'delete' => 'Delete',
    //     ];
    // }

    /**
     * Fungsi hapus data
     *
     */
    // public function delete()
    // {
    //     foreach ($this->getSelected() as $id) {
    //         TabunganModels::where('t_tabungan_id', $id)
    //             ->delete();
    //     }
    //     $this->clearSelected();
    // }
}
