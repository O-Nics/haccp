<?php

declare(strict_types=1);

namespace App\Features\Device\Controllers;

use App\Core\Controllers\Controller;
use App\Features\Device\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Features\Device\Requests\IndexDeviceRequest;
use App\Features\Device\Requests\RevokeOthersRequest;
use App\Features\Device\Resources\DeviceResource;
use App\Features\Device\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * DeviceController handles HTTP device management requests
 * All business logic is delegated to DeviceService
 */
final class DeviceController extends Controller
{
    public function __construct(
        private readonly DeviceService $deviceService,
        private readonly UserDeviceRepositoryInterface $deviceRepository
    ) {}

    /**
     * List all devices for the authenticated user
     */
    public function index(IndexDeviceRequest $request): AnonymousResourceCollection
    {
        $data = $request->toDto();
        $user = auth()->user();

        $paginatedDevices = $this->deviceRepository->getFilteredPaginatedDevices(
            $user,
            $data
        );

        return DeviceResource::collection($paginatedDevices);

    }

    /**
     * Revoke a specific device by ID
     */
    public function destroy(string $deviceId): JsonResponse
    {
        $user = auth()->user();

        $device = $this->deviceRepository->findByUserAndDeviceId($user, $deviceId);

        if (! $device) {
            return response()->json([
                'message' => 'Device not found',
            ], 404);
        }

        $revoked = $this->deviceService->revokeDevice($user, $device);

        if ($revoked) {
            return response()->json([
                'message' => 'Device revoked successfully',
            ]);
        }

        return response()->json([
            'message' => 'Failed to revoke device',
        ], 400);
    }

    /**
     * Revoke all devices except the current one
     */
    public function revokeOthers(RevokeOthersRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->toDto();

        $currentDevice = $this->deviceRepository->findByUserAndDeviceId($user, $data->currentDeviceId);

        if (! $currentDevice) {
            return response()->json([
                'message' => 'Current device not found',
            ], 404);
        }

        $count = $this->deviceService->revokeAllDevicesExcept($user, $currentDevice);

        return response()->json([
            'message' => sprintf('%d device(s) revoked successfully', $count),
            'revoked_count' => $count,
        ]);
    }
}
