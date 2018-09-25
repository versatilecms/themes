<?php

if (!function_exists('themes_path')) {

    /**
     * @param null $theme
     * @return \Illuminate\Config\Repository|mixed|string
     */
    function themes_path($theme = null)
    {
        $path = config('themes.themes_folder', base_path('Themes'));
        if (is_null($theme)) {
            return $path;
        }

        return $path .'/'.$theme;
    }
}

if (!function_exists('theme_assets')) {

    /**
     * @param null $theme
     * @return \Illuminate\Config\Repository|mixed|string
     */
    function theme_assets($theme = null)
    {
        $path = config('themes.assets_path', public_path('themes'));
        if (is_null($theme)) {
            return $path;
        }

        return $path .'/'.$theme;
    }
}

if (!function_exists('has_theme')) {

    /**
     * @return bool
     * @throws Exception
     */
    function has_theme()
    {
        /**
         * @var $themes \Versatile\Themes\Support\Themes
         */
        $themes = app(\Versatile\Themes\Support\Themes::class);
        return (boolean)$themes->allEnabled()->count();
    }
}
