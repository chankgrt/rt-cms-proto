# Filament CMS Prototype
A data synchronization project that seamlessly imports and maintains up-to-date sports data from the SportMonks API into a dedicated database

Built with the following technologies:
- [Laravel 10](https://laravel.com/)
    - [Laravel Sail](https://laravel.com/docs/10.x/sail) - for local docker environment (incl. mysql, redis)
- [Filament 3](https://filamentphp.com/)
- [MySQL 8](https://www.mysql.com/)
- [Redis](https://redis.io/)
- [Docker](https://www.docker.com/)

## Prerequisites
- Docker Service
- Docker Compose
- Make
    - Ubuntu: `sudo apt-get install make`
    - macOS: `brew install make`
    - Windows: via WSL2: `sudo apt-get install make`

This project utilizes a Makefile to automate builds and tasks.

If you don't have make in your machine you can copy-paste the commands in Makefile

You may run `make help` to list available commands.

## How to Setup locally
1. Clone this repo
2. Generate .env file via `make copy-env`, configure as needed.
3. Build and start docker images by running the command below.
```bash
make init
php artisan shield:install
php artisan shield:super-admin
```

4. Voila! App is accessible thru [http://localhost/admin](http://localhost/admin)

## Starting/Stopping the environment
```bash
# Starting
make start
# Stopping
make stop
```

## Creating a user
php artisan make:filament-user
