<?php

    namespace App\Http\Middleware;

    use App\Services\AmoCrmService;
    use Closure;
    use Illuminate\Support\Facades\Session;


    class CheckAmoAuth
    {
        public function handle($request, Closure $next)
        {
            $amo = app(AmoCrmService::class);

            if ($amo->hasValidToken()) {
                return redirect('/leads');
            }

            return $next($request);
        }
    }
