<?php

declare(strict_types=1);

namespace App\Features\Session\Repositories\Device;

use App\Features\Session\Data\DeviceInfoData;
use App\Features\Session\Data\IndexDeviceData;
use App\Models\Device;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DeviceRepositoryInterface
{
    public function findByDeviceId(string $deviceId): ?Device;

    public function findByUserAndDeviceId(User $user, string $deviceId): ?Device;

    public function store(DeviceInfoData $deviceInfoData, User $user): Device;

    public function update(Device $device, DeviceInfoData $deviceInfoData): Device;

    public function getFilteredPaginatedDevices(User $user, IndexDeviceData $data): LengthAwarePaginator;

    public function delete(Device $device): bool;

    public function getAllDevicesExcept(User $user, Device $exceptDevice): Collection;
}
