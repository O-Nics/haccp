<?php

declare(strict_types=1);

namespace App\Features\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\Business\Requests\IndexBusinessRequest;
use App\Features\Business\Resources\BusinessResource;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class BusinessController extends Controller
{
    public function __construct() {}

    /**
     * List all businesses for the authenticated user
     *
     * @return BaseAnonymousCollection A JSON response containing a collection of businesses.
     */
    public function index(IndexBusinessRequest $indexBusinessRequest): BaseAnonymousCollection
    {
        $user = auth()->user();

        $user->load('businesses');

        return BusinessResource::collection($user->businesses);

    }
}
