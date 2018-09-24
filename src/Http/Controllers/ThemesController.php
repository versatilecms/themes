<?php

namespace Versatile\Themes\Http\Controllers;

use Illuminate\Http\Request;
use Versatile\Core\Http\Controllers\Controller;
use Versatile\Themes\Support\Themes;

class ThemesController extends Controller
{
    /**
     * @var Themes
     */
    private $themes;

    public function __construct()
    {
        $this->themes = app(Themes::class);
    }

    /**
     * Anytime the admin visits the theme page we will check if we need to add any more themes to the database
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        $themes = $this->themes
            ->publish()
            ->all();

        return view('themes::index', [
            'themes' => $themes
        ]);
    }

    /**
     * @param $themeFolder
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function activate($themeFolder)
    {
        $theme = $this->themes->find($themeFolder);

        if (is_null($theme)) {
            return redirect()
                ->route("versatile.theme.index")
                ->with([
                    'message' => "Could not find theme " . $themeFolder . ".",
                    'alert-type' => 'error',
                ]);
        }

        $theme->enable();

        return redirect()
            ->route("versatile.theme.index")
            ->with([
                'message' => "Successfully activated " . $theme->name . " theme.",
                'alert-type' => 'success',
            ]);
    }

    /**
     * @param $themeFolder
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deactivate($themeFolder)
    {
        $theme = $this->themes->find($themeFolder);

        if (is_null($theme)) {
            return redirect()
                ->route("versatile.theme.index")
                ->with([
                    'message' => "Could not find theme " . $themeFolder . ".",
                    'alert-type' => 'error',
                ]);
        }

        $theme->disable();

        return redirect()
            ->route("versatile.theme.index")
            ->with([
                'message' => "Successfully deactivated " . $theme->name . " theme.",
                'alert-type' => 'success',
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $theme = $this->themes->find($request->folder);

        if (is_null($theme)) {
            return redirect()
                ->route("versatile.theme.index")
                ->with([
                    'message' => "Could not find theme to delete",
                    'alert-type' => 'error',
                ]);
        }

        $theme->delete();

        return redirect()
            ->back()
            ->with([
                'message' => "Successfully deleted theme " . $theme->name,
                'alert-type' => 'success',
            ]);

    }
}
