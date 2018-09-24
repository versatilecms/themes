<?php

namespace Versatile\Themes\Support;

class View
{
    protected $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Check if a view to thema exists
     *
     * @param $path
     * @return bool
     */
    public function exists($path)
    {
        if (ends_with($path, '.blade.php')) {
            $path = str_replace('.blade.php', '', $path);
        }

        if (strpos($path, '.') !== false) {
            $path = str_replace('.', '/', $path);
        }

        $path = $this->theme->getPath("resources/views/{$path}.blade.php");

        return file_exists($path);
    }

    /**
     * Gets theme layouts
     *
     * @param bool $strict
     * @return array|mixed
     */
    public function layouts($strict = true)
    {
        $layouts = $this->theme->config('page_layouts', null);

        if (is_null($layouts)) {
            return [];
        }

        if (!is_array($layouts)) {
            return [];
        }

        if ($strict == false) {
            return $layouts;
        }

        foreach ($layouts as $layout => $name) {
            if (!$this->exists("layouts.{$layout}")) {
                unset($layouts[$layout]);
            }
        }

        return $layouts;
    }
}
