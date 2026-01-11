<?php

namespace App\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    protected string $model;

    protected function query(): Builder
    {
        return $this->model::query();
    }

    protected function model(): \Illuminate\Database\Eloquent\Model
    {
        return new $this->model;
    }
}
