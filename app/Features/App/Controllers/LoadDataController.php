<?php

namespace App\Features\App\Controllers;

use App\Core\Controllers\Controller;
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
    public function __invoke(): JsonResponse
    {
        $permissions = Permission::all();

        return response()->json([
            'permissions' => $permissions,
        ]);

    }
}
