#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

echo "Starting development environment..."

echo "Bringing up docker services (db, mercure, mailer)..."
docker compose up -d --build

echo "Waiting 5s for services to initialize..."
sleep 5

echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures (will purge DB) ..."
php bin/console doctrine:fixtures:load --no-interaction

echo "Generating a short-lived MERCURE_DEV_JWT for browser subscriptions (1h)..."
secret="${MERCURE_SUBSCRIBER_JWT_KEY:-!ChangeThisMercureHubJWTSecretKey!}"
now=$(date +%s)
payload=$(php -r "echo json_encode(['sub'=>'dev-subscriber','mercure'=>['subscribe'=>['urn:teamrocket:chat:global','urn:teamrocket:chat:team/*']],'iat'=>${now},'exp'=>${now}+3600]);")
header='{"alg":"HS256","typ":"JWT"}'
b64() { php -r "echo rtrim(strtr(base64_encode(stream_get_contents(STDIN)), '+/', '-_'), '=');"; }

token=$(printf '%s' "$header" | b64)
token="$token.$(printf '%s' "$payload" | b64)"
sig=$(printf '%s' "$token" | php -r "\$s=trim(stream_get_contents(STDIN)); \$sig=hash_hmac('sha256', \$s, '$secret', true); echo rtrim(strtr(base64_encode(\$sig), '+/', '-_'), '=');")
token="$token.$sig"

echo "MERCURE_DEV_JWT=$token" > .env.local
echo ".env.local created with MERCURE_DEV_JWT (dev only)"

echo "All done. You can now open http://127.0.0.1:8000 and the Stimulus chat client will use the dev JWT (in dev environment)."
echo "To simulate messages run: php bin/console app:simulate-chat 3"

echo "Tip: inspect hub logs: docker compose logs --tail 200 -f mercure-hub"

exit 0
