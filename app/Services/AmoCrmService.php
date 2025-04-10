<?php

    namespace App\Services;

    use AmoCRM\Client\AmoCRMApiClient;
    use Illuminate\Support\Facades\Session;
    use League\OAuth2\Client\Token\AccessTokenInterface;
    use League\OAuth2\Client\Token\AccessToken;

    class AmoCrmService
    {
        protected AmoCRMApiClient $client;

        public function __construct()
        {
            $this->client = new AmoCRMApiClient(
                config('services.amocrm.client_id'),
                config('services.amocrm.client_secret'),
                config('services.amocrm.redirect')
            );

            if ($this->hasToken()) {
                $this->client->setAccessToken(new AccessToken([
                    'access_token' => Session::get('access_token'),
                    'refresh_token' => Session::get('refresh_token'),
                    'expires' => Session::get('expires'),
                ]));

                $this->client->setAccountBaseDomain(Session::get('base_domain'));
            }
        }

        public function getAuthUrl(): string
        {
            return $this->client->getOAuthClient()->getAuthorizeUrl([
                'redirect_uri' => config('services.amocrm.redirect')
            ]);
        }

        public function hasToken(): bool
        {
            return Session::has('access_token') && Session::has('refresh_token') && Session::has('base_domain');
        }

        public function hasValidToken(): bool
        {
            return Session::has('access_token') && Session::get('expires') > time();
        }

        public function storeToken(AccessTokenInterface $token, string $baseDomain): void
        {
            Session::put([
                'access_token' => $token->getToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires' => $token->getExpires(),
                'base_domain' => $baseDomain,
            ]);
        }

        public function getAccessTokenByCode(string $code, string $referer): AccessTokenInterface
        {
            $this->client->setAccountBaseDomain($referer);

            return $this->client->getOAuthClient()->getAccessTokenByCode($code);
        }

        public function getLeads(int $page = 1, int $limit = 25)
        {
            $filter = new \AmoCRM\Filters\LeadsFilter();
            $filter->setPage($page);
            $filter->setLimit($limit);

            $leads = $this->client->leads()->get($filter, ['contacts']);
            return $leads;
        }

        public function getContactsById(array $id)
        {
            $filter = new \AmoCRM\Filters\ContactsFilter();
            $filter->setIds($id);

            return $this->client->contacts()->get($filter);
        }

        public function getStatusesByPipeline(): array
        {
            $pipelinesCollection = $this->client->pipelines()->get();

            $statusesByPipeline = [];

            foreach ($pipelinesCollection as $pipeline) {
                $pipelineId = $pipeline->getId();
                foreach ($pipeline->getStatuses() as $status) {
                    $statusesByPipeline[$pipelineId][$status->getId()] = $status->getName();
                }
            }

            return $statusesByPipeline;
        }

    }
