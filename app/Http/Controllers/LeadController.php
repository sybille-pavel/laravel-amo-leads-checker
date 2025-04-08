<?php

    namespace App\Http\Controllers;

    use AmoCRM\Client\AmoCRMApiClient;
    use Illuminate\Support\Facades\Session;

    class LeadController extends Controller
    {
        public function index()
        {
            $apiClient = new AmoCRMApiClient(
                config('services.amocrm.client_id'),
                config('services.amocrm.client_secret'),
                config('services.amocrm.redirect_uri')
            );

            $apiClient->setAccessToken(new \League\OAuth2\Client\Token\AccessToken([
                'access_token'  => Session::get('access_token'),
                'refresh_token' => Session::get('refresh_token'),
                'expires'       => Session::get('expires'),
            ]));

            $apiClient->setAccountBaseDomain(config('services.amocrm.base_domain'));

            try {
                $leads = $apiClient->leads()->get();

                return view('leads.index', ['leads' => $leads->all()]);
            } catch (\Exception $e) {
                return 'Ошибка получения лидов: ' . $e->getMessage();
            }
        }
    }
