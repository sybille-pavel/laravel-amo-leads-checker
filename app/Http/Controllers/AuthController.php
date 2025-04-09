<?php

    namespace App\Http\Controllers;

    use App\Services\AmoCrmService;
    use Illuminate\Http\Request;

    class AuthController extends Controller
    {
        protected AmoCrmService $amo;

        public function __construct(AmoCrmService $amo)
        {
            $this->amo = $amo;
        }

        public function redirect()
        {
            return redirect($this->amo->getAuthUrl());
        }

        public function callback(Request $request)
        {
            $code = $request->get('code');
            $referer = $request->get('referer');

            if (!$code || !$referer) {
                return response('Invalid code or referer', 403);
            }

            try {
                $accessToken = $this->amo->getAccessTokenByCode($code, $referer);
                $this->amo->storeToken($accessToken, $referer);

                return redirect('/leads');
            } catch (\Throwable $e) {
                return response('Ошибка авторизации: ' . $e->getMessage(), 500);
            }
        }
    }
