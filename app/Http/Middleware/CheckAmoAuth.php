<?php

    namespace App\Http\Middleware;

    use App\Services\AmoCrmAuthService;
    use Closure;
    use Illuminate\Support\Facades\Session;


    class CheckAmoAuth
    {
        public function handle($request, Closure $next)
        {
            $amo = app(AmoCrmAuthService::class);

            if ($amo->hasValidToken()) {
                return redirect('/leads');
            }

            return $next($request);
        }
    }
