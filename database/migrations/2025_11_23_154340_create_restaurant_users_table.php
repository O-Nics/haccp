<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('restaurant_role_id')->nullable()->constrained('restaurant_roles')->nullOnDelete();

            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->enum('status', ['invited', 'active', 'inactive'])->default('invited');

            $table->timestamps();

            $table->unique(['restaurant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_users');
    }
};
