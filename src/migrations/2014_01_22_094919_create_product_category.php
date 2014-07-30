<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductCategory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('product_category', function(Blueprint $table) {
			$table->increments('id');
            $table->integer("product_id")->unsigned();
            $table->integer("category_id")->unsigned();
            // foreign keys
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('category_id')
                ->references('id')->on('category')
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
        Schema::dropIfExists('product_category');
    }

}
