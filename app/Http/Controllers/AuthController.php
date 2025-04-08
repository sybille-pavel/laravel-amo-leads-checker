<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use AmoCRM\Client\AmoCRMApiClient;

    class AuthController extends Controller
    {
        private function getAmoClient(): AmoCRMApiClient
        {
            $clientId = config('services.amocrm.client_id');
            $clientSecret = config('services.amocrm.client_secret');
            $redirectUri = config('services.amocrm.redirect');

            return new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        }

        public function redirect()
        {
            $apiClient = $this->getAmoClient();

            $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                'redirect_uri' => config('services.amocrm.redirect'),
            ]);

            return redirect($authorizationUrl);
        }

        public function callback(Request $request)
        {
            $code = $request->get('code');
            $referer = $request->get('referer');

            if (!$code) {
                return response('Invalid state or code', 403);
            }

            $apiClient = $this->getAmoClient();
            $apiClient->setAccountBaseDomain($referer);

            try {
                $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($code);


                session([
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'base_domain' => $referer,
                ]);

                return redirect('/leads');
            } catch (\Throwable $e) {
                return response('Ошибка получения токена: ' . $e->getMessage(), 500);
            }
        }
    }
