<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Core\Controllers\BaseController;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * Get authenticated user
     *
     * Returns the currently authenticated user's information.
     */
    public function show(Request $request): UserResource
    {
        $user = auth()->user();

        return new UserResource($user);
    }
}
