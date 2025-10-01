<?php
namespace App\Service;

class MercureJwtFactory
{
    private string $secret;

    public function __construct(string $publisherJwtKey)
    {
        $this->secret = $publisherJwtKey;
    }

    public function createPublisherJwt(): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];
        $payload = [
            'mercure' => [
                'publish' => ['*'],
            ],
            'mercure.publish' => ['*'],
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $base64UrlHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
