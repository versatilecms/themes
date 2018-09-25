<?php

namespace Versatile\Themes\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

use Versatile\Themes\Http\Middleware\HasThemeMiddleware;
use Versatile\Themes\Commands\InstallCommand;
use Versatile\Themes\Support\Theme;
use Versatile\Themes\Support\Themes;
use Versatile\Themes\Support\Breadcrumbs;

class ThemesServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier
     */
    protected $packagePath = __DIR__ . '/../../';

    /**
     * @var string
     */
    protected $themesFolder;

    /**
     * @var Themes
     */
    protected $themes;

    /**
     * @var Theme|null
     */
    protected $theme;

    /**
     * Register the menu options and selected theme.
     *
     * @param Router $router
     * @param Request $request
     * @return string
     */
    public function boot(Router $router, Request $request)
    {
        try {

            $this->themesFolder = config('themes.themes_folder', base_path('Themes'));

            $this->strapRoutes();
            $this->strapViews();
            $this->strapMigrations();
            $this->strapCommands();


            if (!is_null($this->theme)) {

                view()->share('theme', $this->theme);

                $themePath = "{$this->theme->getPath()}/resources/views";
                $this->loadViewsFrom($themePath, 'v-theme');

                // Provide user data to all views
                View::composer('*', function ($view) use ($request) {
                    $view->with('currentUser', \Auth::user());
                    $view->with('breadcrumbs', Breadcrumbs::getBreadcrumbs($request));
                });

                // Use our own paginator view
                Paginator::defaultView('v-theme::partials.pagination');
            }

            $this->loadViewsFrom($this->themesFolder, 'themes-folder');

            $router->aliasMiddleware('theme.check', HasThemeMiddleware::class);

        } catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Register is loaded every time the versatile themes hook is used.
     *
     * @throws \Exception
     */
    public function register()
    {
        $this->loadHelpers();

        $this->strapTheme();

        $this->mergeConfigFrom($this->packagePath . 'config/themes.php', 'themes');
    }

    /**
     * Bootstrap our Routes
     */
    protected function strapRoutes()
    {
        $this->loadRoutesFrom($this->packagePath . 'routes/web.php');
    }

    /**
     * Bootstrap our Views
     */
    protected function strapViews()
    {
        $this->loadViewsFrom($this->packagePath . 'resources/views', 'themes');
    }

    /**
     * Bootstrap our Migrations
     */
    protected function strapMigrations()
    {
        $this->loadMigrationsFrom($this->packagePath . 'database/migrations');
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Bootstrap our Commands/Schedules
     */
    protected function strapCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }

    /**
     * Load current theme
     */
    protected function strapTheme()
    {
        /**
         * @var Themes
         */
        $themes = app(Themes::class);

        /**
         * @var $theme Theme
         */
        $theme = $themes->current();

        if (!is_null($theme)) {
            $theme->provider()->registerPageLayouts();
            $theme->provider()->registerPageBlocks();
            $theme->provider()->registerFields();
            $theme->provider()->loadHelpers();
        }

         $this->themes = $themes;
         $this->theme = $theme;
    }
}
