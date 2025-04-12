<?php
    namespace App\Services\Token;

    use App\Contracts\TokenStorageInterface;
    use Illuminate\Support\Facades\Session;
    use League\OAuth2\Client\Token\AccessToken;
    use League\OAuth2\Client\Token\AccessTokenInterface;


    class SessionTokenStorage implements TokenStorageInterface
    {
        public function get(): ?AccessToken
        {
            if (!$this->has()) {
                return null;
            }

            return new AccessToken([
                'access_token' => Session::get('access_token'),
                'refresh_token' => Session::get('refresh_token'),
                'expires' => Session::get('expires'),
            ]);
        }

        public function store(AccessTokenInterface $token, string $baseDomain): void
        {
            Session::put([
                'access_token' => $token->getToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires' => $token->getExpires(),
                'base_domain' => $baseDomain,
            ]);
        }

        public function has(): bool
        {
            return Session::has('access_token') && Session::has('refresh_token') && Session::has('base_domain');
        }

        public function getBaseDomain(): ?string
        {
            return Session::get('base_domain');
        }

        public function getExpires(): ?int
        {
            return Session::get('expires');
        }
    }
