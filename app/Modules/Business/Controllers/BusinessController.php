<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexBusinessRequest;
use App\Modules\Business\Resources\BusinessResource;

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
