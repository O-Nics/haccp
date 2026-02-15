<?php

namespace Tests\TestHelpers;


use Domain\Auth\Data\DeviceData;

trait DeviceHelper
{
    public function getRandomDevice(): DeviceData
    {
        return new DeviceData(
            deviceId: fake()->uuid(),
            devicePlatform: fake()->randomElement(['ios', 'android', 'web']),
            deviceModel: fake()->randomElement(['iPhone 12', 'Samsung Galaxy S21', 'MacBook Pro']),
            rememberMe: fake()->boolean(),
            ip: fake()->ipv4(),
        );
    }

}
