<?php

namespace Versatile\Themes\Support;

use Versatile\Core\Support\Registry;
use Versatile\Pages\Facades\Blocks as BlocksFacade;
use Versatile\Core\Facades\Versatile as VersatileFacade;
use Versatile\Core\Facades\Fields as FieldsFacade;
use Versatile\Core\Components\Fields\After\DescriptionHandler;
use Versatile\Core\Events\FieldsRegistered;
use Exception;

class Provider
{
    /**
     * @var Theme
     */
    protected $theme;

    /**
     * Provider constructor.
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Register page layouts for the current theme
     */
    public function registerPageLayouts()
    {
        Registry::set('page_layouts', $this->theme->layouts());
    }

    /**
     * Register the page blocks of the current theme
     */
    public function registerPageBlocks()
    {
        $blocks = $this->theme->config('page_blocks', null);

        if (empty($blocks)) {
            return;
        }

        foreach ($blocks as $class) {
            if (!class_exists($class)) {
                throw new Exception('Block class does not exist: '. $class);
            }

            BlocksFacade::add($class);
        }
    }

    /**
     * Register the form fields of the current theme
     */
    public function registerFields()
    {
        $fields = $this->theme->config('form_fields', null);

        if (empty($fields)) {
            return;
        }

        foreach ($fields as $class) {
            if (!class_exists($class)) {
                throw new Exception('Field class does not exist: '. $class);
            }

            FieldsFacade::addFormField($class);
        }

        FieldsFacade::addAfterFormField(DescriptionHandler::class);
        event(new FieldsRegistered($fields));
    }

    /**
     * Loads the current theme's helpers
     */
    public function loadHelpers()
    {
        $path = $this->theme->getPath('Helpers');
        foreach (glob("{$path}/*.php") as $filename) {
            require_once $filename;
        }
    }
}
