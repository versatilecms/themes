<?php

namespace Versatile\Themes\Http\Middleware;

use Closure;

class HasThemeMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return $this|mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!has_theme()) {
            return redirect()->route('versatile.login')->withErrors([
                    'msg' => 'There are no themes currently installed.'
                ]);
        }

        return $next($request);
    }
}
