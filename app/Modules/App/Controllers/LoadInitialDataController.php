<?php

declare(strict_types=1);

namespace App\Modules\App\Controllers;

use App\Core\Controllers\BaseController;
use App\Modules\App\Resources\LoadInitialDataResource;
use Domain\Business\Models\Permission;

class LoadInitialDataController extends BaseController
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

        return new LoadInitialDataResource($permissions);
    }
}
