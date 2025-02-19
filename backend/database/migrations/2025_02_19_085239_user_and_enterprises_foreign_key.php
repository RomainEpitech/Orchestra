<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ajouter la clé étrangère de users vers enterprises
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('enterprise_uuid')
                ->references('uuid')
                ->on('enterprises')
                ->onDelete('cascade');
        });

        // Ajouter la clé étrangère de enterprises vers users (référence circulaire)
        Schema::table('enterprises', function (Blueprint $table) {
            $table->foreign('owner_uuid')
                ->references('uuid')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer la clé étrangère de enterprises vers users
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropForeign(['owner_uuid']);
        });
        
        // Supprimer la clé étrangère de users vers enterprises
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['enterprise_uuid']);
        });
    }
};