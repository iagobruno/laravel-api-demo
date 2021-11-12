# laravel-api-demo

Uma simples API rest para treinar e estudar.

## Objetivos

- [ ] Usar todo o meu conhecimento em Laravel nesse projeto.
- [ ] Usar o Docker desde o início.
- [ ] Testar todas as rotas usando o Pest.
- [ ] Usar o laravel/sanctum para autenticar as solicitações.

## Afazeres

- Criar rota para deletar um tweet

- Criar rota para ver um perfil
- Criar rota para ver tweets de um perfil
- Criar rota para seguir outro perfil
- Criar rota para deixar de seguir um perfil

- Cachear número de tweets e seguidores
- Criar rota de feed para buscar tweets das pessoas que você segue

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

Then start docker containers and run the migrations:

```bash
sail up -d
sail artisan migrate
sail artisan db:seed #Optional
```

## Running tests

To run tests, run the following command:

> NOTE: Make sure you started the docker container first.

```bash
sail test
```
