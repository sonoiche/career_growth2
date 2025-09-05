# Deploy & Operate (Local / CI-friendly)

This service is designed to run **locally/offline** with no third-party credentials.

## Requirements
- PHP 8.3
- Composer 2.7+
- MySQL 8.0+ (or MariaDB 10.6+)
- Redis 7 (recommended; file cache fallback works)
- Laravel 11.x

## First-time Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure DB_* in .env
php artisan migrate

# Seed reference data & taxonomy
php artisan db:seed
# Or: php artisan import:taxonomy --from=./seed
```

## Health & Version
- GET /api/health → returns 200 ok or 503 degraded, checks db+cache
- GET /api/version → git commit + X-Taxonomy-Version value

## Logs
- JSON logs at storage/logs/laravel.json.log
- Each request includes X-Request-Id header and is logged under extra.request_id.

## Cache Warmup (optional)
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Updating the Taxonomy
- After imports or schema changes, bump TAXONOMY_VERSION in .env
(or config/taxonomy.php) so clients can detect updates.

## cURL Smoke Tests
```
curl -i http://127.0.0.1:8000/api/health
curl -i http://127.0.0.1:8000/api/version
curl -i "http://127.0.0.1:8000/api/skills?type=technical&q=react"
```