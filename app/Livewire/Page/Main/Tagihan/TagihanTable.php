<?php

namespace App\Livewire\Page\Main\Tagihan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\TagihanModels;
use App\Traits\MyHelpers;

class TagihanTable extends DataTableComponent
{
    protected $model = TagihanModels::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_tagihan_id')
        ->setTableRowUrl(function($row) {
            return route('main.tagihan.show', ['id' => $row->t_tagihan_id]);
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
            Column::make("Action", "t_tagihan_id")->format(function ($value, $row, $column) {
                return view('livewire.page.main.tagihan.tagihan-action', ['row' => $row]);
            }),
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
            Column::make("Uraian", "uraian")
                ->sortable(),
            Column::make("Jumlah", "jumlah_tagihan")
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $value != Null ? 'Rp. '.$this->toRupiah($value) : '-';
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
        foreach ($this->getSelected() as $id) {
            TagihanModels::where('t_tagihan_id', $id)
                ->delete();
        }
        $this->clearSelected();
    }
}
