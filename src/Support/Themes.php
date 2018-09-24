<?php

namespace Versatile\Themes\Support;

use Versatile\Core\Support\ModuleRepository;
use Illuminate\Container\Container;

class Themes extends ModuleRepository
{

    /**
     * @var string
     */
    protected $jsonFile = 'theme.json';


    /**
     * Themes constructor.
     *
     * @param Container $app
     * @param null $path
     * @throws \Exception
     */
    public function __construct(Container $app, $path = null)
    {
        parent::__construct($app, $path);
        $this->path = config('themes.themes_folder', resource_path('views/themes'));
    }

    protected function createModule($folder)
    {
        return app(Theme::class, ['folder' => $folder]);
    }

    /**
     * @return ModuleRepository|null
     * @throws \Exception
     */
    public function current()
    {
        return $this->allEnabled()->first();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function publish()
    {
        $this->all()->map(function ($item) {
            return $item->publish();
        });

        return $this;
    }
}
