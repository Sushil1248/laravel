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
            // Add the created_by column as an unsigned integer
            $table->unsignedBigInteger('created_by')->nullable();
            // Create a foreign key reference to the id column on the users table
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Remove the created_by column and foreign key constraint
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
