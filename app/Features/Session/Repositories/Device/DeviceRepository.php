<?php

declare(strict_types=1);

namespace App\Features\Session\Repositories\Device;

use App\Core\Repositories\BaseRepository;
use App\Features\Session\Data\DeviceInfoData;
use App\Features\Session\Data\IndexDeviceData;
use App\Models\Device;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DeviceRepository extends BaseRepository implements DeviceRepositoryInterface
{
    public function __construct(
    ) {
        $this->model = Device::class;
    }

    public function findByDeviceId(string $deviceId): ?Device
    {
        return $this->query()->where('device_id', $deviceId)->first();
    }

    public function findByUserAndDeviceId(User $user, string $deviceId): ?Device
    {
        return $user->devices()->where('device_id', $deviceId)->first();
    }

    public function store(DeviceInfoData $deviceInfoData, User $user): Device
    {
        $device = new Device;
        $this->fillDevice($deviceInfoData, $device);

        $user->devices()->save($device);

        return $device;
    }

    public function update(Device $device, DeviceInfoData $deviceInfoData): Device
    {
        $this->fillDevice($deviceInfoData, $device);

        $device->save();

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

    protected function fillDevice(DeviceInfoData $deviceInfoData, Device $device): void
    {
        $device->device_id = $deviceInfoData->deviceId;
        $device->device_name = $deviceInfoData->deviceName;
        $device->device_type = $deviceInfoData->deviceType;
        $device->platform = $deviceInfoData->platform;
        $device->browser = $deviceInfoData->browser;
        $device->ip_address = request()->ip();
        $device->user_agent = $deviceInfoData->userAgent ?? request()->userAgent();
        $device->last_used_at = now();
    }
}
