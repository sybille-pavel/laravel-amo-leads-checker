<?php
// app/Services/AmoCrmAuthService.php
    namespace App\Services;

    use AmoCRM\Client\AmoCRMApiClient;
    use App\Contracts\TokenStorageInterface;
    use League\OAuth2\Client\Token\AccessTokenInterface;

    class AmoCrmAuthService
    {
        public function __construct(
            protected AmoCRMApiClient $client,
            protected TokenStorageInterface $storage
        ) {
            if ($this->hasToken()) {
                $this->client->setAccessToken($this->storage->get());
                $this->client->setAccountBaseDomain($this->storage->getBaseDomain());
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
            return $this->storage->has();
        }

        public function hasValidToken(): bool
        {
            return $this->storage->has() && $this->storage->getExpires() > time();
        }

        public function storeToken(AccessTokenInterface $token, string $baseDomain): void
        {
            $this->storage->store($token, $baseDomain);
        }

        public function getClient(): AmoCRMApiClient
        {
            return $this->client;
        }

        public function getAccessTokenByCode(string $code, string $referer): AccessTokenInterface
        {
            $this->client->setAccountBaseDomain($referer);
            return $this->client->getOAuthClient()->getAccessTokenByCode($code);
        }
    }
