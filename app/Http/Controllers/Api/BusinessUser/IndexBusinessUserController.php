<?php

namespace App\Http\Controllers\Api\BusinessUser;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResponse;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexBusinessUserController extends Controller
{
    /**
     *  Index Business Users
     * * Retrieve a list of business users for a business.
     *
     */
    public function __invoke(Business $business): AnonymousResourceCollection
    {
        $businessUsers = $business->users()->get();

        return UserResource::collection($businessUsers);

    }
}
