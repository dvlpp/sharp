<?php

namespace Dvlpp\Sharp\Http\Middleware;

use Closure;

class SharpGuestMiddleware
{
    use WithLang;
    use WithSharpVersion;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (sharp_auth_guard()->check() && is_sharp_user()) {
            return redirect('/admin');
        }

        $this->addLangToView();
        $this->addVersionToView();

        return $next($request);
    }
}