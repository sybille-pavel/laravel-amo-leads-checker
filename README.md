# Leads Manager API

Простое веб-приложение на **Laravel 11** (API) и **Vue 3** (SPA) с авторизацией через AMO CRM.

---

## 📦 Стек

- **Backend**: Laravel 11 (PHP 8.2)
- **Frontend**: Vue 3 (Vite, Composition API)
- **Package Manager**: NPM 18
- **База данных**: MySQL / SQLite

---

## 🚀 Установка и настройка

### 1. Клонируйте репозиторий

```bash
git clone https://github.com/sybille-pavel/laravel-amo-leads-checker
cd laravel-amo-leads-checker
```
### 2. Настройка Backend (Laravel)
```bash
composer install
cp .env.example .env
```
### 3. Настройка AMO CRM

- AMOCRM_CLIENT_ID=
- AMOCRM_CLIENT_SECRET=
- AMOCRM_REDIRECT_URI=http://localhost:8000/auth/callback
- AMOCRM_BASE_DOMAIN=

### 5. Запуск сервера
```bash
php artisan serve
```

### 3. Настройка AMO CRM
```bash
npm install
npm run dev
```
