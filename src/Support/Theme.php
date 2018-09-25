<?php

namespace Versatile\Themes\Support;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Versatile\Core\Traits\Seedable;
use Versatile\Core\Support\Json;
use Versatile\Core\Contracts\ModuleInterface;

class Theme implements ModuleInterface
{
    use Seedable;

    protected $seedersPath;

    /**
     * @var string
     */
    public $assetsPath;

    /**
     * @var string
     */
    public $themesFolder;

    /**
     * @var array
     */
    public $theme;

    /**
     * @var string
     */
    protected $path;


    /**
     * @var array
     */
    public $config = [];

    /**
     * @var array of cached Json objects, keyed by filename
     */
    protected $moduleJson = [];

    /**
     * Theme constructor.
     * @param $folder
     * @throws Exception
     */
    public function __construct($folder)
    {
        $this->assetsPath = config('versatile-themes.assets_path', public_path('themes'));
        $this->themesFolder = config('versatile-themes.themes_folder', base_path('Themes'));

        $this->handle($folder);

        return $this;
    }

    /**
     * @param $folder
     * @return $this
     * @throws Exception
     */
    public function handle($folder)
    {
        if (!$this->exists($folder)) {
            throw new Exception('Theme not found: ' . $folder);
        }

        $this->path = $this->themesFolder . '/' . $folder;

        return $this;
    }

    /**
     * Get path.
     *
     * @param null $path
     * @return string
     */
    public function getPath($path = null)
    {
        if (is_null($path)) {
            return $this->path;
        }

        if (starts_with($path, '/')) {
            return $this->path . $path;
        }

        return $this->path . '/' . $path;
    }

    /**
     * Enable the current module.
     *
     * @return $this
     */
    public function enable()
    {
        $seeder = $this->path . '/Seeder.php';
        if (file_exists($seeder)) {
            $this->seedersPath = $this->path . '/';
            $this->seed('Seeder');
        }

        $this->setActive(1);

        return $this;
    }

    /**
     * Disable the current module.
     *
     * @return $this
     */
    public function disable()
    {
        $seeder = $this->path . '/Unseeder.php';
        if (file_exists($seeder)) {
            $this->seedersPath = $this->path . '/';
            $this->seed('Unseeder');
        }

        $this->setActive(0);

        return $this;
    }

    /**
     * Determine whether the given status same with the current module status.
     *
     * @param $status
     *
     * @return bool
     */
    public function isStatus($status) : bool
    {
        return $this->get('active', 0) === $status;
    }

    /**
     * Determine whether the current module activated.
     *
     * @return bool
     */
    public function enabled() : bool
    {
        return $this->isStatus(1);
    }

    /**
     *  Determine whether the current module not disabled.
     *
     * @return bool
     */
    public function disabled() : bool
    {
        return !$this->enabled();
    }

    /**
     * Set active state for current module.
     *
     * @param $active
     *
     * @return bool
     */
    public function setActive($active)
    {
        return $this->json()->set('active', $active)->save();
    }

    /**
     * @return $this
     */
    public function publish()
    {
        if (config('versatile-themes.publish_assets', true) === false) {
            return $this;
        }

        $themePath = theme_assets($this->folder);

        if (!file_exists($themePath)) {
            if (!file_exists($this->assetsPath)) {
                mkdir($this->assetsPath);
            }
            mkdir($themePath, 0775);
        }

        $assets = $this->path . '/public/assets';
        if (file_exists($assets)) {
            File::copyDirectory($assets, theme_assets($this->folder));
        }

        $screenshot = $this->path . '/screenshot.jpg';
        if (file_exists($screenshot)) {
            File::copy($screenshot, theme_assets($this->folder) . '/screenshot.jpg');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function delete()
    {
        if (file_exists($this->path)) {
            File::deleteDirectory($this->path, false);
        }

        if (file_exists(theme_assets($this->folder))) {
            File::deleteDirectory(theme_assets($this->folder), false);
        }
    }

    /**
     * @param string $folder
     * @return bool
     */
    private function exists($folder)
    {
        $jsonFile = themes_path($folder . '/theme.json');
        if (!file_exists($jsonFile)) {
            return false;
        }

        return true;
    }

    public function assets($file = null)
    {
        $path = url('themes/' . $this->folder);

        if (is_null($file)) {
            return $path;
        }

        return $path .'/'. $file;
    }


    /**
     * @param null $key
     * @param null $default
     * @return array|mixed
     */
    public function config($key = null, $default = null)
    {
        $path = $this->path . '/config.php';
        if (file_exists($path)) {
            $this->config = include $path;
        }

        if (is_null($key)) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * @return View
     */
    public function view()
    {
        return (new View($this));
    }

    /**
     * @return Provider
     */
    public function provider()
    {
        return (new Provider($this));
    }

    /**
     * Gets theme layouts
     *
     * @param bool $strict
     * @return array|mixed
     */
    public function layouts($strict = true)
    {
        return (new View($this))->layouts($strict);
    }

    /**
     * Get json contents from the cache, setting as needed.
     *
     * @param string $file
     *
     * @return Json
     */
    public function json($file = null) : Json
    {
        if ($file === null) {
            $file = 'theme.json';
        }
        return array_get($this->moduleJson, $file, function () use ($file) {
            return $this->moduleJson[$file] = new Json($this->getPath() . '/' . $file);
        });
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * Handle call to __get method.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }


    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->folder;
    }
}
