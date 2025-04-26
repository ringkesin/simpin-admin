<?php

namespace App\Livewire\Page\Main\Konten;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\ContentModels;
use App\Traits\MyHelpers;

class KontenTable extends DataTableComponent
{
    protected $model = ContentModels::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_content_id')
        ->setTableRowUrl(function($row) {
            return route('main.konten.show', ['id' => $row->t_content_id]);
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
            Column::make("ID", "t_content_id")
                ->sortable()
                ->searchable(),
            Column::make("Tipe Konten", "contentType.content_name")
                ->sortable()
                ->searchable(),
            Column::make("Judul Konten", "content_title")
                ->sortable()
                ->searchable(),
            Column::make("Valid Dari", "valid_from")
                ->sortable(),
            Column::make("Valid Sampai", "valid_to")
                ->sortable(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'reject' => 'Reject',
        ];
    }
}
