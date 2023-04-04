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
        Schema::table('roles', function (Blueprint $table) {
            // drop the existing index
            $table->dropUnique('roles_name_guard_name_unique');

            // add a new unique index
            $table->unique(['name', 'created_by'], 'name_created_by_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            // drop the unique index
            $table->dropUnique('name_created_by_unique');

            // recreate the old index
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};
