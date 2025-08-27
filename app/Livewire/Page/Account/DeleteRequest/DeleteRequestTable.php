<?php

namespace App\Livewire\Page\Account\DeleteRequest;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Main\DeleteAccountRequestModels;
use App\Traits\MyHelpers;
use Illuminate\Support\HtmlString;

class DeleteRequestTable extends DataTableComponent
{
    protected $model = DeleteAccountRequestModels::class;

    use MyHelpers;

    public function configure(): void
    {
        $this->setPrimaryKey('t_delete_account_requests_id');
        // ->setTableRowUrl(function($row) {
        //     return route('main.konten.show', ['id' => $row->t_delete_account_requests_id]);
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
            Column::make("ID", "t_delete_account_requests_id")
                ->hideIf(true),
            // Column::make("Status", "status")
            //     ->sortable()
            //     ->searchable(),
            Column::make("Status", "status")
            ->format(function ($value, $row) {
                // tentukan warna berdasarkan status
                $colors = [
                    'open'  => 'bg-yellow-500 hover:bg-yellow-600',
                    'approved' => 'bg-green-500 hover:bg-green-600',
                    'rejected' => 'bg-red-500 hover:bg-red-600',
                ];

                $color = $colors[$value] ?? 'bg-gray-500';

                // route untuk update status
                $url = route('account.delete-request.execute', ['id' => $row->t_delete_account_requests_id]);

                return new HtmlString(
                    "<a href='{$url}' class='px-3 py-1 rounded text-white {$color}'>
                        " . ucfirst($value) . "
                    </a>"
                );
            }),
            Column::make("Fullname", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Username", "user.username")
                ->sortable()
                ->searchable(),
            Column::make("Keterangan/Alasan", "remarks"),
            Column::make("Tgl. Permintaan", "created_at")
                ->sortable(function(Builder $query, string $direction) {
                    return $query->orderBy('created_at', $direction);
                })
                ->searchable(function($query, $searchTerm) {
                    $query->orWhere('created_at', 'ilike', "%{$searchTerm}%");
                }),
        ];
    }
}
