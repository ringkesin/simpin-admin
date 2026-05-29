<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Events\SearchApplied;

trait CaseInsensitiveTableSearch
{
    public function applySearch(): Builder
    {
        if ($this->searchIsEnabled() && $this->hasSearch()) {
            $searchableColumns = $this->getSearchableColumns();
            $search = $this->getSearch();

            $this->callHook('searchUpdated', ['value' => $search]);
            $this->callTraitHook('searchUpdated', ['value' => $search]);

            if ($this->getEventStatusSearchApplied() && $search != null) {
                event(new SearchApplied($this->getTableName(), $search));
            }

            if ($searchableColumns->count()) {
                $this->setBuilder($this->getBuilder()->where(function ($query) use ($searchableColumns, $search) {
                    foreach ($searchableColumns as $index => $column) {
                        if ($column->hasSearchCallback()) {
                            ($column->getSearchCallback())($query, $search);
                        } else {
                            $wrappedColumn = $this->getBuilder()->getGrammar()->wrap($column->getColumn());
                            $query->{$index === 0 ? 'whereRaw' : 'orWhereRaw'}(
                                'CAST('.$wrappedColumn.' AS TEXT) ILIKE ?',
                                ['%'.$search.'%']
                            );
                        }
                    }
                }));
            }
        }

        return $this->getBuilder();
    }
}
