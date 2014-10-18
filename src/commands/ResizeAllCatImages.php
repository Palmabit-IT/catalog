<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResizeAllCatImages extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'category:resizeimages';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize all images in categories.';

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
        $size = $this->argument('size');

        $category_repository = App::make('category_repository');
        $category_repository->resizeAllImages($size);

        $this->info("Image resized succesfully");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'size',
                InputArgument::REQUIRED,
                'The size of the new images.'
            ],
        ];
    }

}
