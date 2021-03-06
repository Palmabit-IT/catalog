<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableProduct extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('product', function(Blueprint $table) {
			$table->increments('id');
            $table->string('code')->nullable();
            // for single table inheritance
            $table->string('type');
//            $table->string('name');
            // nullable only when you duplicate a product
//            $table->string('slug')->nullable();
//			$table->text('description', 60);
//            $table->text('long_description')->nullable();
            $table->boolean('featured')->default(0);
            $table->integer("order")->unsigned()->default(0);
            $table->boolean("blocked")->default(0);
            $table->boolean("public")->default(1);
            $table->boolean("offer")->default(1);
            $table->boolean("with_vat")->default(0);
            $table->boolean("stock")->default(0);
            $table->integer("professional")->default(0);
            $table->string("video_link")->nullable()->default(null);
            $table->decimal("price1", 19,2)->nullable();
            $table->decimal("price2", 19,2)->nullable();
            $table->decimal("price3", 19,2)->nullable();
            $table->decimal("price4", 19,2)->nullable();
            $table->boolean("quantity_pricing_enabled")->default(0);
            $table->integer("quantity_pricing_quantity")->default(0);
            $table->integer("quantity_pricing_quantity_non_professional")->default(0);
            // for multilanguage
            // nullable when you duplicate a product
//            $table->string('slug_lang')->nullable();
//            $table->string('lang',2)->default('it');
//            $table->unique(array('slug_lang', 'lang'));
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
        Schema::dropIfExists('product');
    }

}
