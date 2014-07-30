<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('order', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('user_id');
                $table->dateTime('date');
                $table->boolean('completed');
                $table->timestamps();
                $table->softDeletes();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('order');
    }

}
