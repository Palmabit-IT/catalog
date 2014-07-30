<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('products_products', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer("first_product_id")->unsigned();
                $table->integer("second_product_id")->unsigned();
                // foreign keys
                $table->foreign('first_product_id')
                    ->references('id')->on('product')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->foreign('second_product_id')
                    ->references('id')->on('product')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('products_products');

    }

}
