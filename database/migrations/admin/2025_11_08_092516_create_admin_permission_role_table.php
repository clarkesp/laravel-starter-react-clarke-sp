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
        Schema::create('admin_permission_role', function (Blueprint $table) {
            $table->foreignId('admin_permission_id')->constrained('admin_permissions')->onDelete('cascade');
            $table->foreignId('admin_role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['admin_permission_id', 'admin_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permission_role');
    }
};
