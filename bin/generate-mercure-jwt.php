<?php

require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

$key = $argv[1] ?? 'dev-secret-key';     // clé JWT passée en argument ou valeur par défaut
$type = $argv[2] ?? 'publisher';         // 2e argument : "publisher" ou "subscriber"

// Payload Mercure
$payload = [
    'mercure' => []
];

if ($type === 'publisher') {
    $payload['mercure']['publish'] = ['*'];   // autorise la publication sur tous les topics
} elseif ($type === 'subscriber') {
    $payload['mercure']['subscribe'] = ['*']; // autorise l'abonnement à tous les topics
} else {
    fwrite(STDERR, "Usage: php bin/generate-mercure-jwt.php <key> [publisher|subscriber]\n");
    exit(1);
}

$jwt = JWT::encode($payload, $key, 'HS256');

echo $jwt;
