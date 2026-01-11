<?php

namespace App\Features\User\Controllers;

use App\Core\Controllers\Controller;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Features\User\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    /**
     * Get authenticated user
     *
     * Returns the currently authenticated user's information.
     */
    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        return apiResponse(new UserResource($user));
    }
}
