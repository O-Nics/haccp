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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_type_id')->constrained('restaurant_types')->onDelete('cascade');
            $table->string('name');
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
        Schema::dropIfExists('restaurants');
    }
};
