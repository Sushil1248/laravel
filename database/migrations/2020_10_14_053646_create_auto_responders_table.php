<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoRespondersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasTable('auto_responders')) {
            Schema::create('auto_responders', function (Blueprint $table) {            
				$table->bigIncrements('id');
				$table->string('subject');
				$table->string('template_name');
				$table->longText('template');
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
        Schema::dropIfExists('auto_responders');
    }
}
