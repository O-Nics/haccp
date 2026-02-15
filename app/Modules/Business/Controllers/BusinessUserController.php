<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexBusinessUserRequest;
use App\Modules\Business\Requests\InviteUserRequest;
use App\Modules\Business\Resources\BusinessUserResource;
use Domain\Business\Data\IndexBusinessUserData;
use Domain\Business\Interfaces\BusinessUserRepositoryInterface;
use Domain\Business\Services\BusinessUserService;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessUserController handles HTTP requests for business user management.
 */
final class BusinessUserController extends BaseController
{
    public function __construct(
        private readonly BusinessUserService $businessUserService,
        private readonly BusinessUserRepositoryInterface $businessUserRepository
    ) {}

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     */
    public function index(IndexBusinessUserRequest $request): BaseAnonymousCollection
    {
        $businessUsers = $this->businessUserRepository->getPaginated(
            $request->business(),
            $request->toDto(),
        );

        return BusinessUserResource::collection($businessUsers);
    }

    /**
     * Invite a user to a business
     *
     * Sends an invitation to a user to join the specified business.
     */
    public function store(InviteUserRequest $request): BaseAnonymousCollection
    {
        $business = $request->business();

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
