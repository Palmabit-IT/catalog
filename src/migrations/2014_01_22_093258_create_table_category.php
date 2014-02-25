<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableCategory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if(! Schema::hasTable('category'))
            Schema::create('category', function(Blueprint $table) {
                $table->increments('id');
                $table->string('description');
                $table->string('slug')->unique();
                // for hierarchy with nested sets
                $table->integer('parent_id')->nullable();
                $table->integer('lft')->nullable();
                $table->integer('rgt')->nullable();
                $table->integer('depth')->nullable();
                // for multilang
                $table->string('slug_lang');
                $table->string('lang',2)->default('it');
                $table->timestamps();
                $table->softDeletes();
            });
        DB::statement('ALTER TABLE  `category` ADD  `image` LONGBLOB');

    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('category');
    }

}
