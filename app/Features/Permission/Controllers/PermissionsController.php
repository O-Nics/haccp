<?php

declare(strict_types=1);

namespace App\Features\Permission\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\Permission\Resources\PermissionResource;
use App\Models\Business;
use App\Models\Permission;

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
    public function index(): BaseAnonymousCollection
    {
        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }
}
