<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Force Sanctum to use tokens instead of sessions in tests
        config(['sanctum.stateful' => []]);
    }
}
