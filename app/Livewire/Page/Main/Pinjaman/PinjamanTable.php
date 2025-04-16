<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\PinjamanModels;
use App\Traits\MyHelpers;

class PinjamanTable extends DataTableComponent
{
    protected $model = PinjamanModels::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_pinjaman_id')
        ->setTableRowUrl(function($row) {
            return route('main.pinjaman.show', ['id' => $row->t_pinjaman_id]);
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
            Column::make("ID", "t_pinjaman_id")
                ->sortable()
                ->searchable(),
            Column::make("Nomor Anggota", "masterAnggota.nomor_anggota")
                ->sortable()
                ->searchable(),
            Column::make("Nama", "masterAnggota.nama")
                ->sortable()
                ->searchable(),
            Column::make("Tgl Pengajuan", "created_at")
                ->sortable()
                ->searchable(),
            Column::make("Jenis Pinjaman", "masterJenisPinjaman.nama")
                ->sortable()
                ->searchable(),
            Column::make("Jumlah Pengajuan", "ra_jumlah_pinjaman")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Jumlah Disetujui", "ri_jumlah_pinjaman")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
                }),
            Column::make("Status Pinjaman", "masterStatusPengajuan.nama")
                ->sortable()
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return $value == 'Pending' ? '<span class="text-sm font-semibold text-white px-1.5 bg-blue-500 rounded-full">'.$value.'</span>' : ($value == 'Approve' ? '<span class="text-sm font-semibold text-white px-1.5 bg-green-500 rounded-full">'.$value.'</span>' : '<span class="text-sm font-semibold text-white px-1.5 bg-rose-500 rounded-full">'.$value.'</span>');
                })->html(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'reject' => 'Reject',
        ];
    }

    /**
     * Fungsi Reject data
     *
     */
    public function reject()
    {
        foreach ($this->getSelected() as $id) {
            PinjamanModels::where('t_pinjaman_id', $id)
                ->update([
                    'p_status_pengajuan_id' => 2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth()->user()->id
                ]);
        }
        $this->clearSelected();
    }
}
