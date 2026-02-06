<?php

namespace App\Features\App\Controllers;

use App\Core\Controllers\Controller;
use App\Features\App\Data\LoadInitialDataData;
use App\Features\App\Resources\LoadInitialDataResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class LoadInitialDataController extends Controller
{
    /**
     * Load initial data
     *
     * Loads initial data required for the application.
     *
     * @unauthenticated
     */
    public function __invoke(): LoadInitialDataResource
    {
        $permissions = Permission::all();

        return new LoadInitialDataResource(
            new LoadInitialDataData(
                permissions: $permissions,
            )
        );

    }
}
