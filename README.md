# Todolist BE

Backend API for a Todo List application.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Database: PostgreSQL (recommended)
- Export CSV: `maatwebsite/excel`

Note: some queries use PostgreSQL-specific features such as `ILIKE` and `FILTER (...)`.

## Setup

1) Install dependencies

```bash
composer install
```

2) Copy env + generate key

```bash
copy .env.example .env
php artisan key:generate
```

3) Configure database in `.env`, then migrate + seed

```bash
php artisan migrate
php artisan db:seed
```

4) Run the server

```bash
php artisan serve
```

## API

Base URL (default): `http://localhost:8000/api`

### 1) Create Todo

`POST /todos`

Body (JSON):

```json
{
	"title": "Learn Laravel",
	"assignee": "John",
	"due_date": "2025-12-31",
	"time_tracked": 30,
	"status": "pending",
	"priority": "high"
}
```

Valid values:

- `status`: `pending`, `open`, `in_progress`, `completed`
- `priority`: `low`, `medium`, `high`

### 2) Export Todos (CSV)

`GET /todos/export`

Query params (optional):

- `title`: string (partial match)
- `assignees`: comma-separated (example: `John,Jane`)
- `statuses`: comma-separated (example: `pending,completed`)
- `priorities`: comma-separated (example: `low,high`)
- `start` + `end`: `due_date` range (date format)
- `min` / `max`: range `time_tracked` (minutes)

Example:

```bash
curl -L "http://localhost:8000/api/todos/export?statuses=completed&min=10&max=120" -o todos.csv
```

### 3) Chart Summary

`GET /chart?type=...`

Allowed `type` values:

- `status` → `status_summary`
- `priority` → `priority_summary`
- `assignee` → `assignee_summary`

Example:

```bash
curl "http://localhost:8000/api/chart?type=assignee"
```

