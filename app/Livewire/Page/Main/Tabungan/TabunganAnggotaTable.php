<?php

namespace App\Livewire\Page\Main\Tabungan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\VTabunganJurnal;
use App\Models\Main\TabunganJurnalModels;
use App\Traits\MyHelpers;
use Illuminate\Support\Facades\DB;

class TabunganAnggotaTable extends DataTableComponent
{
    protected $model = VTabunganJurnal::class;
    public $p_anggota_id;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_tabungan_jurnal_id');
        // ->setTableRowUrl(function($row) {
            // return route('main.tabungan.update', ['id' => $row->t_tabungan_jurnal_id]);
        // });
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
            Column::make("ID", "t_tabungan_jurnal_id")
                ->hideIf(true),
            Column::make("Tgl. Transaksi", "tgl_transaksi_beautify")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('tgl_transaksi_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Jenis Tabungan", "jenis_tabungan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('jenis_tabungan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Total", "nilai_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('nilai', $direction);
                })
                ->searchable(),
            Column::make("Total s.d. Tgl Transaksi", "nilai_sd_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('nilai_sd', $direction);
                })
                ->searchable(),
            Column::make("Remarks", "catatan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('catatan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Tgl. Diinput", "created_at_beautify")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('created_at_beautify', 'ilike', "%{$searchTerm}%");
                }),
            
            
            // Column::make("Total", "nilai")
            //     ->sortable(function(Builder $query, string $direction) {
            //         return $query->orderBy('nilai', $direction);
            //     })
            //     ->searchable()
            //     ->footer(function ($rows) {
            //         return number_format($rows->sum('nilai'), 2, ',', '.'); // or use a helper
            //     }),

            // Column::make("Total s.d.", "nilai_sd")
            //     ->sortable(function(Builder $query, string $direction) {
            //         return $query->orderBy('nilai_sd', $direction);
            //     })
            //     ->searchable()
            //     ->footer(function ($rows) {
            //         return number_format($rows->sum('nilai_sd'), 2, ',', '.');
            //     }),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'delete' => 'Delete',
        ];
    }

    /**
     * Fungsi hapus data
     *
     */
    public function delete()
    {
        $data = TabunganJurnalModels::whereIn('t_tabungan_jurnal_id', $this->getSelected())->orderBy('tgl_transaksi', 'asc')->first()->toArray();
        $tahun = date('Y', strtotime($data['tgl_transaksi']));
        
        TabunganJurnalModels::whereIn('t_tabungan_jurnal_id', $this->getSelected())->delete();

        DB::select('SELECT _tabungan_recalculate(:p_anggota_id, :tahun)', [
            'p_anggota_id' => $this->p_anggota_id,
            'tahun' => $tahun,
        ]);
        $this->clearSelected();
    }

    public function builder(): Builder
    {
        return VTabunganJurnal::query()
            ->where('p_anggota_id', $this->p_anggota_id)
            ->select();
    }
}
