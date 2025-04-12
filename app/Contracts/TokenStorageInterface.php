<?php
// app/Contracts/TokenStorageInterface.php
    namespace App\Contracts;

    use League\OAuth2\Client\Token\AccessTokenInterface;

    interface TokenStorageInterface
    {
        public function get(): ?AccessTokenInterface;
        public function store(AccessTokenInterface $token, string $baseDomain): void;
        public function has(): bool;
        public function getBaseDomain(): ?string;
        public function getExpires(): ?int;
    }
