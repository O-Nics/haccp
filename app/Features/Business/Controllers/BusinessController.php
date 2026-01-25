<?php

declare(strict_types=1);

namespace App\Features\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\Business\Requests\IndexBusinessRequest;
use App\Features\Business\Requests\IndexBusinessUserRequest;
use App\Features\Business\Requests\InviteUserRequest;
use App\Features\Business\Resources\BusinessResource;
use App\Features\Business\Services\BusinessService;
use App\Features\User\Data\IndexBusinessData;
use App\Features\User\Data\IndexBusinessUserData;
use App\Features\User\Resources\BusinessUserResource;
use App\Models\Business;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class BusinessController extends Controller
{
    public function __construct(private readonly BusinessService $businessService, private readonly BusinessRepositoryInterface $businessRepository) {}

    /**
     * List all businesses for the authenticated user
     */
    public function index(IndexBusinessRequest $indexBusinessRequest): AnonymousResourceCollection
    {
        $user = auth()->user();

        $user->load('businesses');

        return BusinessResource::collection($user->businesses);

    }

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     *
     * @param  Business  $business  The business for which to retrieve users.
     * @param  IndexBusinessUserRequest  $indexBusinessUserRequest  The request containing pagination and filtering
     * @return AnonymousResourceCollection A JSON response containing a collection of business users.
     */
    public function businessUsers(Business $business, IndexBusinessUserRequest $indexBusinessUserRequest): AnonymousResourceCollection
    {
        $businessUsers = $this->businessRepository->getBusinessUsersPaginated(
            $business,
            $indexBusinessUserRequest->toDto(),
        );

        return BusinessUserResource::collection($businessUsers);

    }

    public function inviteUser(Business $business, InviteUserRequest $inviteUserRequest): AnonymousResourceCollection
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
