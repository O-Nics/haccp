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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_type_id')->constrained('business_types')->cascadeOnDelete();
            $table->string('name');
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('siret', 14)->unique();
            $table->string('address');
            $table->string('postal_code', 10);
            $table->string('city');
            $table->string('country', 2);
            $table->string('phone')->nullable();
            $table->string('email')->unique();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
