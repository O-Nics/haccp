<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public function scopeFiltered(Builder $query, ?string $filterQuery = null, array $searchableColumns = []): void
    {
        if ($filterQuery && ! empty($searchableColumns)) {
            $query->where(function (Builder $query) use ($filterQuery, $searchableColumns) {
                $isFirst = true;

                foreach ($searchableColumns as $column) {
                    if ($column === 'fullname') {
                        $filterQueryLower = strtolower($filterQuery);

                        $method = $isFirst ? 'whereRaw' : 'orWhereRaw';

                        $query->$method(
                            "LOWER(CONCAT(TRIM(firstname), ' ', TRIM(lastname))) LIKE ? OR LOWER(CONCAT(TRIM(lastname), ' ', TRIM(firstname))) LIKE ?",
                            ["%{$filterQueryLower}%", "%{$filterQueryLower}%"]
                        );
                    } else {
                        $method = $isFirst ? 'whereLike' : 'orWhereLike';
                        $query->$method($column, "%{$filterQuery}%");
                    }

                    $isFirst = false;
                }
            });
        }
    }

    public function scopeOrder(Builder $query, ?string $sort, ?string $order): void
    {
        if ($sort) {
            $query->orderBy($sort, $order ?? 'asc');
        } else {
            $query->orderBy('created_at');
        }
    }

    public function scopeBetweenDates(Builder $query, $column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false): void
    {
        if ($startAt || $endAt) {
            $start = $startAt ?? now()->format('Y-m-d');
            $end = $endAt ?? now()->format('Y-m-d');

            $query->whereBetween($column, [$start, $end], $boolean, $not);
        }
    }

    public function scopeForPeriod(Builder $query, ?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at'): void
    {
        if ($startAt || $endAt) {
            $start = $startAt ?? now()->format('Y-m-d');
            $end = $endAt ?? now()->format('Y-m-d');

            $query->where(function (Builder $q) use ($start, $end, $columnStart, $columnEnd) {
                $q->where($columnStart, '<=', $end)
                    ->where($columnEnd, '>=', $start);
            });
        }
    }
}
