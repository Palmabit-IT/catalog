<?php namespace Palmabit\Catalog\Install;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'catalog:install';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install catalog package.';

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
    $this->call('config:publish', ['package' => 'palmabit/catalog' ] );

    $this->call('migrate', ['--package' => 'palmabit/catalog'] );

    $this->call('asset:publish');

    $this->info('## Palmabit Catalog Installed successfully ##');
  }
}
