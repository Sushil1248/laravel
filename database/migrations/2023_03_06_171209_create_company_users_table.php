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
        if (!Schema::hasTable('company_users')) {
            Schema::create('company_users', function (Blueprint $table) {
              $table->bigIncrements('id');
              $table->unsignedBigInteger('user_id');
              $table->unsignedBigInteger('company_id');
              $table->tinyInteger('status')->nullable();
              $table->timestamps();
            });
          }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_users');
    }
};
