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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->json('authority')->nullable();
            $table->string('color_hex')->default('#6B7280');
            $table->uuid('enterprise_uuid')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->timestamps();
            
            $table->foreign('enterprise_uuid')->references('uuid')->on('enterprises')->onDelete('cascade');
        });
        
        // Ajouter role_uuid à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('role_uuid')->nullable()->after('enterprise_uuid');
            $table->foreign('role_uuid')->references('uuid')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_uuid']);
            $table->dropColumn('role_uuid');
        });
        
        Schema::dropIfExists('roles');
    }
};