<?php

namespace App\Features\App\Controllers;

use App\Core\Controllers\Controller;
use App\Features\App\Data\LoadDataData;
use App\Features\App\Resources\LoadDataResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class LoadDataController extends Controller
{
    /**
     * Load initial data
     *
     * Loads initial data required for the application.
     *
     * @unauthenticated
     */
    public function __invoke(): LoadDataResource
    {
        $permissions = Permission::all();

        return new LoadDataResource(
            new LoadDataData(
                permissions: $permissions,
            )
        );

    }
}
