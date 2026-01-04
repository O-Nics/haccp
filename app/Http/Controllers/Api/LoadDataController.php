<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResponse;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\AuthService;
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
