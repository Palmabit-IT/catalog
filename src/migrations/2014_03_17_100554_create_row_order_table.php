<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('row_order', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->decimal("total_price", 19,2);
            $table->decimal('single_price', 19,2);
            $table->string('price_type_used');
            $table->string('slug_lang')->nullable();
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
		Schema::dropIfExists('row_order');
	}

}
