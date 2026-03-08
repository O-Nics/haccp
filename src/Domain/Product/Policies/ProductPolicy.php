<?php

declare(strict_types=1);

namespace Domain\Product\Policies;

use App\Core\Policy\BasePolicy;
use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Product\Models\Product;
use Illuminate\Auth\Access\Response;

final class ProductPolicy extends BasePolicy
{
    public function viewAny(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::PRODUCT_VIEW_ANY);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::PRODUCT_VIEW);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::PRODUCT_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::PRODUCT_EDIT);
    }

    /**
     * Determines whether the user can bulk-delete products for the given business.
     */
    public function deleteMany(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::PRODUCT_DELETE);
    }
}
