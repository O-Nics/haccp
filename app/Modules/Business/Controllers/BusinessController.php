<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexBusinessRequest;
use App\Modules\Business\Resources\BusinessResource;
use Domain\Business\Interfaces\BusinessRepositoryInterface;

final class BusinessController extends BaseController
{
    public function __construct(private BusinessRepositoryInterface $businessRepository) {}

    /**
     * List all businesses for the authenticated user
     *
     * @return BaseAnonymousCollection A JSON response containing a collection of businesses.
     */
    public function index(IndexBusinessRequest $indexBusinessRequest): BaseAnonymousCollection
    {
        $user = auth()->user();

        $paginatedBusinesses = $this->businessRepository->indexBusinessPaginated($user, $indexBusinessRequest->toDto());

        return BusinessResource::collection($paginatedBusinesses);
    }
}
