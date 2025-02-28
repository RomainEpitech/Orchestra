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
        Schema::create('enterprise_modules', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('enterprise_uuid');
            $table->uuid('module_uuid');
            $table->string('status')->default('active');
            $table->boolean('is_premium')->default(false);
            $table->timestamps();

            $table->unique(['enterprise_uuid', 'module_uuid']);
            
            $table->foreign('enterprise_uuid')
                ->references('uuid')
                ->on('enterprises')
                ->onDelete('cascade');

            $table->foreign('module_uuid')
                ->references('uuid')
                ->on('modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise_modules');
    }
};