<?php

declare(strict_types=1);

namespace App\Features\Device\Repositories\UserDevice;

use App\Features\Device\Data\DeviceInfoData;
use App\Features\Device\Data\IndexDeviceData;
use App\Models\Device;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

readonly class UserDeviceRepository implements UserDeviceRepositoryInterface
{
    public function __construct(
        private Device $model
    ) {}

    public function findByDeviceId(string $deviceId): ?Device
    {
        return $this->model->where('device_id', $deviceId)->first();
    }

    public function findByUserAndDeviceId(User $user, string $deviceId): ?Device
    {
        return $user->devices()->where('device_id', $deviceId)->first();
    }

    public function store(DeviceInfoData $deviceInfoData, int $expirationMinutes, User $user): Device
    {
        $refreshToken = Device::generateRefreshToken();

        $device = new Device;
        $this->fillDevice($deviceInfoData, $device, $expirationMinutes, $refreshToken);

        $user->devices()->save($device);

        $device->setAttribute('plain_refresh_token', $refreshToken);

        return $device;
    }

    public function update(Device $device, DeviceInfoData $deviceInfoData, int $expirationMinutes): Device
    {
        $refreshToken = Device::generateRefreshToken();

        $this->fillDevice($deviceInfoData, $device, $expirationMinutes, $refreshToken);

        $device->save();

        $device->setAttribute('plain_refresh_token', $refreshToken);

        return $device;
    }

    public function getFilteredPaginatedDevices(User $user, IndexDeviceData $data): LengthAwarePaginator
    {
        $searchableColumns = ['device_name', 'device_id', 'browser', 'ip_address'];

        return $user->devices()
            ->filtered($data->filterQuery, $searchableColumns)
            ->order($data->sort, $data->order)
            ->paginate(
                perPage: $data->perPage,
                page: $data->page,
            );
    }

    public function rotateRefreshToken(Device $device, int $expirationMinutes): string
    {
        $newRefreshToken = Device::generateRefreshToken();
        $device->setRefreshToken($newRefreshToken);
        $device->token_expires_at = now()->addMinutes($expirationMinutes);
        $device->touchLastUsed();
        $device->save();

        return $newRefreshToken;
    }

    public function delete(Device $device): bool
    {
        return $device->delete();
    }

    public function getAllDevicesExcept(User $user, Device $exceptDevice): Collection
    {
        return $user->devices()
            ->where('id', '!=', $exceptDevice->id)
            ->get();
    }

    protected function fillDevice(DeviceInfoData $deviceInfoData, Device $device, int $expirationMinutes, string $refreshToken): void
    {
        $device->device_id = $deviceInfoData->deviceId;
        $device->device_name = $deviceInfoData->deviceName;
        $device->device_type = $deviceInfoData->deviceType;
        $device->platform = $deviceInfoData->platform;
        $device->browser = $deviceInfoData->browser;
        $device->ip_address = request()->ip();
        $device->user_agent = $deviceInfoData->userAgent ?? request()->userAgent();
        $device->last_used_at = now();
        $device->token_expires_at = now()->addMinutes($expirationMinutes);
        $device->setRefreshToken($refreshToken);
    }
}
