<?php

use Illuminate\Console\Command;
use Palmabit\Catalog\Services\AlignProducts;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AlignProductsLang extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'products:align';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run a bulk update on products of same code to use the same slug lang in every language.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $align = new AlignProducts();

        $align->cleanProducts();
        $align->alignData();

        $this->info("Products aligned succesfully");
	}

}
