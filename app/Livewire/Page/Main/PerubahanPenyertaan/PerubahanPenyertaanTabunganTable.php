<?php

namespace App\Livewire\Page\Main\PerubahanPenyertaan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\VPerubahanPenyertaanTabungan;
use App\Traits\MyHelpers;
use App\Traits\MyAlert;

class PerubahanPenyertaanTabunganTable extends DataTableComponent
{
    protected $model = VPerubahanPenyertaanTabungan::class;

    use MyHelpers;
    use MyAlert;

    public function configure(): void
    {
        $this->setPrimaryKey('perubahan_penyertaan_id')
        ->setTableRowUrl(function($row) {
            return route('main.perubahan-penyertaan.approval', ['id' => $row->perubahan_penyertaan_id]);
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
            Column::make("ID", "perubahan_penyertaan_id")
                ->hideIf(true),
            Column::make("Tgl. Mulai", "tgl_mulai_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('valid_from', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('tgl_mulai_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Nama Anggota", "nama_anggota")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nama_anggota', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Jenis Tabungan", "jenis_tabungan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('jenis_tabungan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Nilai Sebelum", "nilai_sebelum_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('jumlah', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nilai_sebelum_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Nilai Baru", "nilai_baru_beautify")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('jumlah', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('nilai_baru_beautify', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Catatan User", "catatan_user")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('catatan_user', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Status Approval", "status_perubahan_penyertaan")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('status_perubahan_penyertaan', 'ilike', "%{$searchTerm}%");
                }),
            Column::make("Catatan Admin", "catatan_approver")
                ->sortable()
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('catatan_approver', 'ilike', "%{$searchTerm}%");
                }),
        ];
    }

    public function isTableRowSelectable($model): bool
    {
        return $model->status_penyertaan !== 'DISETUJUI';
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

    }
}
