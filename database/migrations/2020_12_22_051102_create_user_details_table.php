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
      if (!Schema::hasTable('user_details')) {
        Schema::create('user_details', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('user_id');
          $table->string('address')->nullable();
          $table->string('mobile')->nullable();
          $table->string('fax_no')->nullable();
          $table->unsignedBigInteger('city_id')->nullable();
          $table->unsignedBigInteger('state_id')->nullable();
          $table->unsignedBigInteger('country_id')->nullable();
          $table->string('zipcode')->nullable();
          $table->date('dob')->nullable();
          $table->binary('profile_picture')->nullable();
          $table->string('imagetype')->nullable();
          $table->text('current_photos')->nullable();
          $table->text('bio')->nullable();
          $table->string('cover_image')->nullable();
          $table->boolean('notification')->default(0);
          $table->enum('gender', ['male', 'female'])->nullable();
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_details');
    }
};
