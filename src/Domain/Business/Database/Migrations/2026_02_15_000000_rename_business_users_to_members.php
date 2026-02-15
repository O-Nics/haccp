<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('business_users', 'members');
    }

    public function down(): void
    {
        Schema::rename('members', 'business_users');
    }
};
