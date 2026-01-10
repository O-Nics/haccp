<?php

declare(strict_types=1);

namespace App\Features\Business\Controllers;

use App\Features\Business\Requests\BusinessResource;
use App\Http\Controllers\Base\Controller;

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

        return BusinessResource::collection($user->businesses);

    }
}
