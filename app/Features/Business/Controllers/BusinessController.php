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
use Illuminate\Http\JsonResponse;
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
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $user->load('businesses');

        return apiResponse(BusinessResource::collection($user->businesses));

    }

    /**
     * Get users of a business
     *
     * Returns a list of users associated with the specified business.
     *
     * @param Business $business The business for which to retrieve users.
     * @param IndexBusinessUserRequest $indexBusinessUserRequest The request containing pagination and filtering
     *
     * @return JsonResponse A JSON response containing a collection of business users.
     */
    public function businessUsers(Business $business, IndexBusinessUserRequest $indexBusinessUserRequest): JsonResponse
    {
        $businessUsers = $this->businessRepository->getBusinessUsersPaginated(
            $business,
            $indexBusinessUserRequest->toDto(),
        );

        return apiResponse(BusinessUserResource::collection($businessUsers));

    }

    public function inviteUser(Business $business, InviteUserRequest $inviteUserRequest): JsonResponse
    {
        $user = $this->businessService->inviteUserToBusiness($business, $inviteUserRequest->toDto());

        return apiResponse(new BusinessUserResource($user), Response::HTTP_CREATED);
    }
}
