<?php

declare(strict_types=1);

namespace App\Repositories\UserDevice;

use App\DataTransferObjects\Device\IndexDeviceData;
use App\DataTransferObjects\DeviceInfoData;
use App\Models\Device;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserDeviceRepositoryInterface
{
    public function findByDeviceId(string $deviceId): ?Device;

    public function findByUserAndDeviceId(User $user, string $deviceId): ?Device;

    public function store(DeviceInfoData $deviceInfoData, int $expirationMinutes, User $user): Device;

    public function update(Device $device, DeviceInfoData $deviceInfoData, int $expirationMinutes): Device;

    public function getFilteredPaginatedDevices(User $user, IndexDeviceData $data): LengthAwarePaginator;

    public function rotateRefreshToken(Device $device, int $expirationMinutes): string;

    public function delete(Device $device): bool;

    public function getAllDevicesExcept(User $user, Device $exceptDevice): Collection;
}
