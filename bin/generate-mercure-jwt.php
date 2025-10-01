<?php
// Usage: php bin/generate-mercure-jwt.php '!ChangeThisMercureHubJWTSecretKey!'
$key = $argv[1] ?? getenv('MERCURE_PUBLISHER_JWT_KEY') ?? '!ChangeThisMercureHubJWTSecretKey!';
$header = ['alg' => 'HS256', 'typ' => 'JWT'];
$payload = [
    'mercure' => [ 'publish' => ['*'] ],
    'iat' => time(),
    'exp' => time() + 3600,
];
function b64($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
$jwt = b64(json_encode($header)) . '.' . b64(json_encode($payload));
$jwt .= '.' . b64(hash_hmac('sha256', $jwt, $key, true));
echo $jwt;
