<?php

declare(strict_types=1);

namespace App\Features\Permission\Controllers;

use App\Core\Controllers\Controller;
use App\Features\Business\Requests\IndexBusinessRequest;
use App\Features\Business\Resources\BusinessResource;
use App\Models\Business;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class PermissionsController extends Controller
{
    public function __construct() {}

    /**
     * List all businesses for the authenticated user
     */
    public function index(): AnonymousResourceCollection
    {
        $permissions = Permission::all();
//        return BusinessResource::collection($user->businesses);

    }
}
