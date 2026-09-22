<?php

namespace App\Livewire\Page\Master\RekeningKkba;

use App\Models\Master\RekeningKkba;
use App\Traits\CaseInsensitiveTableSearch;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RekeningKkbaTable extends DataTableComponent
{
    use CaseInsensitiveTableSearch;

    protected $model = RekeningKkba::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('nama_bank', 'asc')
            ->setTableRowUrl(fn ($row) => route('master.rekening-kkba.show', $row->id))
            ->setPerPageAccepted([10, 25, 50, 100]);

        $this->setComponentWrapperAttributes(['default' => true, 'class' => 'rounded-none']);
        $this->setTableWrapperAttributes(['default' => true, 'class' => 'mt-6 p-0 rounded-none']);
        $this->setTableAttributes(['default' => true, 'class' => 'table-auto border-y-1 w-full']);
        $this->setTheadAttributes([
            'default' => true,
            'class' => 'bg-slate-50 text-slate-500 border-slate-200 uppercase border-y text-xs',
        ]);
        $this->setThAttributes(fn (Column $column) => ['default' => true, 'class' => 'py-3 pl-5 px-2']);
        $this->setThSortButtonAttributes(fn (Column $column) => ['class' => 'font-semibold text-left']);
    }

    public function builder(): Builder
    {
        return RekeningKkba::query();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Bank', 'nama_bank')->sortable()->searchable(),
            Column::make('Nomor Rekening', 'nomor_rekening')->sortable()->searchable(),
            Column::make('Atas Nama', 'atas_nama')->sortable()->searchable(),
            Column::make('Status', 'is_active')
                ->sortable()
                ->format(fn ($value) => $value ? 'Aktif' : 'Tidak Aktif'),
            Column::make('Keterangan', 'keterangan')->searchable(),
        ];
    }
}
