<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            // Device ID managed by client (unique per user only)
            $table->string('device_id');

            $table->string('device_name');
            $table->string('device_type', 20)->default('unknown');
            $table->string('platform', 20)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Token management (tokens expire, not devices)

            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'device_id']);
            $table->index('token_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
