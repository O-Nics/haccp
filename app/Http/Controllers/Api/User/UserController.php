<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Base\Controller;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

        return UserResource::make($user);
    }

    public function businessUsers(Business $business): AnonymousResourceCollection
    {
        $businessUsers = $business->users()->get();

        return UserResource::collection($businessUsers);

    }
}
