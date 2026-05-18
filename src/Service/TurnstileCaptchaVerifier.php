<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TurnstileCaptchaVerifier
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $turnstileSecretKey,
    ) {
    }

    public function verify(?string $token, ?string $ipAddress): bool
    {
        if ($token === null || trim($token) === '') {
            return false;
        }

        if (trim($this->turnstileSecretKey) === '') {
            return false;
        }

        try {
            $response = $this->httpClient->request('POST', self::VERIFY_URL, [
                'body' => [
                    'secret' => $this->turnstileSecretKey,
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ],
                'timeout' => 5,
            ]);

            $data = $response->toArray(false);

            return ($data['success'] ?? false) === true;
        } catch (\Throwable) {
            return false;
        }
    }
}