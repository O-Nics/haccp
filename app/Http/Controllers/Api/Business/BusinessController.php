<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Base\Controller;
use App\Http\Resources\BusinessUserResource;
use App\Services\Device\DeviceService;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class BusinessController extends Controller
{
    /**
     * List all businesses for the authenticated user
     */
    public function index()
    {
        $user = auth()->user();

        $user->load('businesses');

        return BusinessUserResource::collection($user->businesses);

    }
}
