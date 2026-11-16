<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var list<string>
     */
    private const AVAILABLE = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->query('lang');

        if (is_string($lang) && in_array($lang, self::AVAILABLE, true)) {
            $request->session()->put('locale', $lang);
            app()->setLocale($lang);
        } else {
            $locale = (string) $request->session()->get('locale', config('app.locale'));

            if (! in_array($locale, self::AVAILABLE, true)) {
                $locale = (string) config('app.locale');
            }

            app()->setLocale($locale);
        }

        return $next($request);
    }
}
