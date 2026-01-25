<?php

declare(strict_types=1);

namespace App\Features\BusinessUser\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\BusinessUser\Data\IndexBusinessUserData;
use App\Features\BusinessUser\Repositories\BusinessUserRepositoryInterface;
use App\Features\BusinessUser\Requests\IndexBusinessUserRequest;
use App\Features\BusinessUser\Requests\InviteUserRequest;
use App\Features\BusinessUser\Resources\BusinessUserResource;
use App\Features\BusinessUser\Services\BusinessUserService;
use App\Models\Business;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessUserController handles HTTP requests for business user management.
 * All business logic is delegated to BusinessUserService.
 */
final class BusinessUserController extends Controller
{
    public function __construct(
        private readonly BusinessUserService $businessUserService,
        private readonly BusinessUserRepositoryInterface $businessUserRepository
    ) {}

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     *
     * @param  Business  $business  The business for which to retrieve users.
     * @param  IndexBusinessUserRequest  $request  The request containing pagination and filtering.
     * @return BaseAnonymousCollection A JSON response containing a collection of business users.
     */
    public function index(Business $business, IndexBusinessUserRequest $request): BaseAnonymousCollection
    {
        $businessUsers = $this->businessUserRepository->getPaginated(
            $business,
            $request->toDto(),
        );

        return BusinessUserResource::collection($businessUsers);
    }

    /**
     * Invite a user to a business
     *
     * Sends an invitation to a user to join the specified business.
     *
     * @param  Business  $business  The business to which the user is being invited.
     * @param  InviteUserRequest  $request  The request containing user invitation details.
     * @return BaseAnonymousCollection A JSON response containing the updated list of business users.
     */
    public function create(Business $business, InviteUserRequest $request): BaseAnonymousCollection
    {
        $this->businessUserService->inviteUserToBusiness($business, $request->toDto());

        $indexBusinessUserDto = new IndexBusinessUserData(
            filterQuery: null,
            sort: 'first_name',
            order: 'asc',
            perPage: 25,
            page: 1,
        );

        $businessUsers = $this->businessUserRepository->getPaginated(
            $business,
            $indexBusinessUserDto,
        );

        return BusinessUserResource::collection($businessUsers)->status(Response::HTTP_CREATED);
    }
}
