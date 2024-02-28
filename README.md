# laravel-api-demo

Uma simples API rest similar ao do Twitter para treinar e estudar.

## Getting started

Clone this repo and run commands in the order below:

```bash
composer install
cp .env.example .env # And edit the values
touch database/database.sqlite
php artisan migrate --seed
```

Then start the server:

```bash
php artisan serve
```

## API Documentation

Once the server is running, you can read the API documentation in [/docs](http://localhost:8000/docs).

## Running tests

To run tests, run the following command:

```bash
php artisan test
# php artisan test --stop-on-failure
# php artisan test --filter GetUserTest
```
