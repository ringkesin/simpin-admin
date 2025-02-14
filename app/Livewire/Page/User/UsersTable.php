<?php

namespace App\Livewire\Page\User;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
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
            Column::make("Action", "id")->format(function ($value, $row, $column) {
                return view('livewire.page.user.user-table-action', ['row' => $row]);
            }),
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Username", "username")
                ->sortable()
                ->searchable(),
            Column::make("Email", "email")
                ->sortable()
                ->searchable(),
            Column::make("Mobile", "mobile")
                ->sortable()
                ->searchable(),
            Column::make("Remarks", "remarks")
                ->sortable(),
            Column::make("Valid from", "valid_from")
                ->sortable(),
            Column::make("Valid until", "valid_until")
                ->sortable(),
            Column::make("Remarks", "remarks")
                ->sortable(),
            Column::make("Profile photo path", "profile_photo_path")
                ->sortable(),
            Column::make("Created by", "created_by")
                ->sortable(),
            Column::make("Updated by", "updated_by")
                ->sortable(),
            Column::make("Deleted by", "deleted_by")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
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
