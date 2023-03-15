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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Product plan');
            $table->float('price');
            $table->string('billing_interval')->comment('day, week, month or year');
            $table->integer('billing_interval_count')->comment('Maximum of one year interval allowed (1 year, 12 months, or 52 weeks)');
            $table->string('product_id');
            $table->string('price_id');
            $table->text('description')->nullable();
            $table->text('features_offered')->nullable();
            $table->text('raw_response')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};
