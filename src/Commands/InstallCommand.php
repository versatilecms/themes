<?php

namespace Versatile\Themes\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

use Versatile\Posts\Providers\PostsServiceProvider;
use Versatile\Core\Traits\Seedable;
use Versatile\Themes\Support\Themes;
use Versatile\Themes\Providers\ThemesServiceProvider;

class InstallCommand extends Command
{
    use Seedable;

    protected $seedersPath = __DIR__ . '/../../database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'versatile-themes:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Versatile Themes package';


    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Publishing Themes module assets, database, and config files');
        $this->call('vendor:publish', ['--provider' => ThemesServiceProvider::class]);

        $this->info('Seeding data into the database');
        $this->seed('ThemesBread');

        /**
         * @var Themes
         */
        $themes = app(Themes::class);
        
        $this->info('Scanning templates');
        $themes->publish();

        $this->info('Successfully installed Versatile Themes! Enjoy');
    }
}
