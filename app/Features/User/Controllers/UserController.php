<?php

namespace App\Features\User\Controllers;

use App\Core\Controllers\Controller;
use App\Features\User\Resources\UserResource;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

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
