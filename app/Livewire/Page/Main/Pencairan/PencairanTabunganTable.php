<?php

namespace App\Livewire\Page\Main\Pencairan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\VPencairanTabungan;
use App\Models\Main\TabunganPengambilanModels;
use App\Traits\MyHelpers;

class PencairanTabunganTable extends DataTableComponent
{
    protected $model = VPencairanTabungan::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('pencairan_id')
        ->setTableRowUrl(function($row) {
            return route('main.pencairan.approval', ['id' => $row->pencairan_id]);
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
            Column::make("ID", "pencairan_id")
                ->hideIf(true),
            Column::make("Tgl. Pengajuan", "tgl_pengajuan_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('tgl_pengajuan', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('tgl_pengajuan_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Nama Anggota", "nama_anggota")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nama_anggota', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Pencairan Tabungan", "jenis_tabungan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('jenis_tabungan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Jumlah Diajukan", "jumlah_diambil_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('jumlah_diambil', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('jumlah_diambil_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Alasan Pencairan", "catatan_user")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('catatan_user', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Status Approval", "status_pengambilan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('status_pengambilan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Jumlah Disetujui Admin", "jumlah_disetujui_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('jumlah_disetujui', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('jumlah_disetujui_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Tgl. Pencairan", "tgl_pencairan_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('tgl_pencairan', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('tgl_pencairan_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Catatan Admin", "catatan_approver")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('catatan_approver', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Rekening No.", "rekening_no")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('rekening_no', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Rekening Bank", "rekening_bank")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('rekening_bank', 'ilike', "%{$searchTerm}%");
                }),
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
        TabunganPengambilanModels::whereIn('t_tabungan_pengambilan_id', $this->getSelected())->delete();
        $this->clearSelected();
    }
}
