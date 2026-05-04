# Dynamic Quiz System

A flexible quiz system built with Laravel that supports multiple question types, media uploads, and automatic evaluation.

## Requirements

- PHP 8.2+
- Composer
- Node.js
- SQLite (default) or MySQL

## Setup Instructions

### 1. Clone or download the project

```bash
cd C:\
git clone <repo-url> quiz-system
cd quiz-system
```

### 2. Install dependencies

```bash
composer install
```

### 3. Set up environment

```bash
copy .env.example .env
php artisan key:generate
```

### 4. Configure database

For SQLite (default, no setup needed):

DB_CONNECTION=sqlite

For MySQL, update `.env`:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_system
DB_USERNAME=root
DB_PASSWORD=your_password

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Link storage for media uploads

```bash
php artisan storage:link
```

### 7. Start the server

```bash
php artisan serve
```

Visit: http://127.0.0.1:8000

## Features

- Create quizzes with title and description
- Add 5 question types: Binary, Single Choice, Multiple Choice, Number, Text
- Upload images for questions and options
- Embed YouTube videos in questions
- Attempt any quiz with your name
- Automatic scoring and result display
- Answer breakdown showing correct answers