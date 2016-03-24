<?php

namespace Dvlpp\Sharp\Http\Middleware;

use Closure;

class SharpAuthMiddleware
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
        if (sharp_auth_guard()->guest() || !is_sharp_user()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/admin/login');
            }
        }

        $this->addLangToView();
        $this->addVersionToView();

        return $next($request);
    }
}