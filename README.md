# HackAccess API

REST API for managing participant registrations at an inclusive university hackathon.

## Tech Stack
- Laravel 11 (PHP 8.3)
- MySQL 8
- Laravel API Resources (JSON transformation layer)

## Endpoints

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/api/participants` | List all participants (paginated) |
| POST | `/api/participants` | Register a new participant |
| DELETE | `/api/participants/{id}` | Remove a participant |

## Setup

```bash
git clone https://github.com/YOUR_USER/hackaccess-api
cd hackaccess-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Environment Variables

Copy `.env.example` to `.env` and fill in your database credentials. Never commit `.env`.
