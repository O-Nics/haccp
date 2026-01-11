<?php

declare(strict_types=1);

namespace App\Features\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\Business\Requests\IndexBusinessUserRequest;
use App\Features\Business\Requests\InviteUserRequest;
use App\Features\Business\Resources\BusinessResource;
use App\Features\Business\Services\BusinessService;
use App\Features\User\Resources\BusinessUserResource;
use App\Models\Business;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class BusinessController extends Controller
{
    public function __construct(private readonly BusinessService $businessService, private readonly BusinessRepositoryInterface $businessRepository)
    {

    }
    /**
     * List all businesses for the authenticated user
     */
    public function index()
    {
        $user = auth()->user();

        $user->load('businesses');

        return BusinessResource::collection($user->businesses);

    }

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     * @param Business $business
     * @return AnonymousResourceCollection
     */
    public function businessUsers(Business $business, IndexBusinessUserRequest $indexBusinessUserRequest): AnonymousResourceCollection
    {
        $businessUsers = $this->businessRepository->getBusinessUsersPaginated(
            $business,
            $indexBusinessUserRequest->toDto(),
        );

        return BusinessUserResource::collection($businessUsers);

    }

    public function inviteUser(Business $business, InviteUserRequest $inviteUserRequest): BusinessUserResource
    {
        $user =  $this->businessService->inviteUserToBusiness($business, $inviteUserRequest->toDto());
        return new BusinessUserResource($user);
    }


}
