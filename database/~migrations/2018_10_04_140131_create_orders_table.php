<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
//    order statuses:
//    0 -> new
//    1 -> accepted
//    2 -> carrier_user found
//    3 -> order conflict (needs editing)
//    4 -> moving started
//    5 -> moving done
//    6 -> all done
//    7 -> cancelled

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('carrier_id');
            $table->integer('origin_address_id');
            $table->integer('dest_address_id');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->integer('price');
            $table->integer('moving_workers');
            $table->integer('packing_workers');
            $table->integer('origin_floor');
            $table->integer('dest_floor');
            $table->integer('origin_walking');
            $table->integer('dest_walking');
            $table->dateTime('moving_time');
            $table->string('signature')->nullable();
            $table->integer('status');
            $table->integer('seen');
            $table->integer('created_on')->default(0); // 0 => app, 1 => panel
            $table->timestamps();
            $table->string('gender');
            $table->integer('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
