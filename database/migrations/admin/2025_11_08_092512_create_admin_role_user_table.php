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
        Schema::create('admin_role_user', function (Blueprint $table) {
            $table->foreignId('admin_user_id')->constrained('admin_users')->onDelete('cascade');
            $table->foreignId('admin_role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['admin_user_id', 'admin_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role_user');
    }
};
