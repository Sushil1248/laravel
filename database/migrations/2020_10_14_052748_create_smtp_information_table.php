<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmtpInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  
		if (!Schema::hasTable('smtp_information')) {
            Schema::create('smtp_information', function (Blueprint $table) {            
				$table->bigIncrements('id');
				$table->string('host');
				$table->string('port');
				$table->string('username')->nullable();
				$table->string('from_email');
				$table->string('from_name');
				$table->string('password');
				$table->string('encryption');
				$table->tinyInteger('status');
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
        Schema::dropIfExists('smtp_information');
    }
}
