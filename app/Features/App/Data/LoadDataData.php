<?php

namespace App\Features\App\Data;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class LoadDataData extends Data
{
    public function __construct(
        /** @var Collection<Permission> */
        public Collection $permissions,
    ) {}

}
