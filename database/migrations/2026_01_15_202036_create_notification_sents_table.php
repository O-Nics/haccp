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
        Schema::create('notification_sents', function (Blueprint $table) {
            $table->id();

            $table->string('recipient'); // email or phone
            $table->string('name'); // notification name
            $table->enum('type', ['sms', 'email', 'liberlo']);
            $table->enum('status', ['sent', 'error', 'blocked']);

            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->datetime('resent_at')->nullable();
            $table->string('error_message')->nullable();

            $table->timestamps();

            $table->index('recipient');
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_sents');
    }
};
