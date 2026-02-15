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
            $table->foreignUuid('user_id')->constrained('users');

            $table->string('recipient'); // email or phone
            $table->string('name'); // notification name
            $table->string('type');
            $table->string('status'); // sent, failed, pending

            $table->string('subject')->nullable();
            $table->longText('content_html')->nullable();
            $table->longText('content_text')->nullable();
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
