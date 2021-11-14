# laravel-api-demo

Uma simples API rest similar ao do Twitter para treinar e estudar.

## Objetivos

- [ ] Usar todo o meu conhecimento em Laravel nesse projeto.
- [ ] Usar o Docker desde o início.
- [ ] Testar todas as rotas usando o Pest.
- [ ] Usar o laravel/sanctum para autenticar as solicitações.

## Built with

- [Laravel](https://laravel.com/)
- [Docker](https://docker.com/)
- [Laravel/sail](https://laravel.com/docs/8.x/sail)
- [Pest](https://pestphp.com/)

## Getting started

Clone this repo and run commands in the order below:

```bash
composer install
cp .env.example .env # And edit the values
php artisan key:generate
```

Then start Docker containers:

```bash
sail up -d
```

And run the migrations:

```bash
sail artisan migrate
sail artisan db:seed #Optional
```

## Running tests

To run tests, run the following command:

```bash
sail artisan test
# sail artisan test --filter GetUserTest
# sail artisan test --stop-on-failure
```

> NOTE: Make sure you started the docker containers first.
