<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Core\Controllers\Controller;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get authenticated user
     *
     * Returns the currently authenticated user's information.
     */
    public function user(Request $request): UserResource
    {
        $user = auth()->user();

        return new UserResource($user);
    }
}
