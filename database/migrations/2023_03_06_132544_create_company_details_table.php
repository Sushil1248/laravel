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
          if (!Schema::hasTable('company_details')) {
            Schema::create('company_details', function (Blueprint $table) {
              $table->bigIncrements('id');
              $table->unsignedBigInteger('company_id');
              $table->string('address')->nullable();
              $table->string('fax_no')->nullable();
              $table->unsignedBigInteger('city_id')->nullable();
              $table->unsignedBigInteger('state_id')->nullable();
              $table->unsignedBigInteger('country_id')->nullable();
              $table->string('zipcode')->nullable();
              $table->date('establish_date')->nullable();
              $table->binary('company_logo')->nullable();
              $table->string('imagetype')->nullable();
              $table->text('current_photos')->nullable();
              $table->text('details')->nullable();
              $table->string('cover_image')->nullable();
              $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('company_details');
    }
};
