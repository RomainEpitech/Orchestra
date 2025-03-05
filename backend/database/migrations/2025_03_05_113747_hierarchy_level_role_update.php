<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->integer('hierarchy_level')->default(100)->after('is_shared');
        });
        
        DB::table('roles')
            ->where('name', 'Administrateur')
            ->where('is_shared', true)
            ->update(['hierarchy_level' => 1]);
            
        DB::table('roles')
            ->where('name', 'Employé')
            ->where('is_shared', true)
            ->update(['hierarchy_level' => 6]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('hierarchy_level');
        });
    }
};