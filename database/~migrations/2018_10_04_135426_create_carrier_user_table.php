<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarrierUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('carrier_id');
            $table->integer('user_id');
            $table->integer('parent_id')->nullable();
            $table->float('rating')->default(5);
            $table->integer('status')->default(1);
            $table->integer('commission')->default(0);
            $table->char('national_code', 10);
            $table->integer('gender')->default(0); // 0 => male, 1 => female
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
        Schema::dropIfExists('carrier_user');
    }
}
