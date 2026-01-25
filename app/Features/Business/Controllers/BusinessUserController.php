<?php

declare(strict_types=1);

namespace App\Features\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\Business\Requests\IndexBusinessUserRequest;
use App\Features\Business\Requests\InviteUserRequest;
use App\Features\Business\Resources\BusinessUserResource;
use App\Features\Business\Services\BusinessService;
use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class BusinessUserController extends Controller
{
    public function __construct(private readonly BusinessService $businessService, private readonly BusinessRepositoryInterface $businessRepository) {}

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     *
     * @param  Business  $business  The business for which to retrieve users.
     * @param  IndexBusinessUserRequest  $indexBusinessUserRequest  The request containing pagination and filtering
     * @return BaseAnonymousCollection A JSON response containing a collection of business users.
     */
    public function index(Business $business, IndexBusinessUserRequest $indexBusinessUserRequest): BaseAnonymousCollection
    {
        $businessUsers = $this->businessRepository->getBusinessUsersPaginated(
            $business,
            $indexBusinessUserRequest->toDto(),
        );

        return BusinessUserResource::collection($businessUsers);

    }

    /**
     * Invite a user to a business
     *
     * Sends an invitation to a user to join the specified business.
     *
     * @param  Business  $business  The business to which the user is being invited.
     * @param  InviteUserRequest  $inviteUserRequest  The request containing user invitation details.
     * @return BaseAnonymousCollection A JSON response containing the updated list of business users.
     */

    public function create(Business $business, InviteUserRequest $inviteUserRequest): BaseAnonymousCollection
    {
        $this->businessService->inviteUserToBusiness($business, $inviteUserRequest->toDto());
        $indexBusinessUserDto = new IndexBusinessUserData(
            filterQuery: null,
            sort: $this->sort ?? 'first_name',
            order: $this->order ?? 'asc',
            perPage: $this->per_page ?? 25,
            page: $this->page ?? 1,
        );
        $businessUsers = $this->businessRepository->getBusinessUsersPaginated(
            $business,
            $indexBusinessUserDto,
        );

        return BusinessUserResource::collection($businessUsers)->status(Response::HTTP_CREATED);

    }
}
