PHPUNIT     = ./vendor/bin/phpunit
PHPSTAN     = ./vendor/bin/phpstan --memory-limit=1G
PHPINSIGHTS = ./vendor/bin/phpinsights
SAIL        = ./vendor/bin/sail
ARTISAN     = php artisan

.PHONY: shell start stop init-install install update test phpstan phpinsights standards lint-fix ide-helper db-up db-reset key-gen cache-clear copy-env init

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

shell: ## login to sail shell container
	$(SAIL) shell

webshell: ## login to web/laravel container
	$(SAIL) exec laravel.test bash

start: ## start the docker services
	$(SAIL) up -d

stop: ## down docker services
	$(SAIL) stop

init-install: ## Initial Installation of deps. Used if the host doesn't have compatible PHP or Composer
	docker run --rm \
	-u "$$(id -u):$$(id -g)" \
	-v $$(pwd):/var/www/html \
	-w /var/www/html \
	laravelsail/php81-composer:latest \
	composer install --ignore-platform-reqs

install: ## Install all php libraries
	$(SAIL) composer install

update: ## Update all php libraries
	$(SAIL) composer update

test: ## run tests
	$(SAIL) $(ARTISAN) test

phpstan: ## run phpstan
	-$(SAIL) exec laravel.test $(PHPSTAN)

phpinsights: ## run phpinsights
	-$(SAIL) exec laravel.test $(PHPINSIGHTS)

standards: phpstan phpinsights ## check if code complies to standards

lint-fix: ## fixes phpinsights
	$(SAIL) $(PHPINSIGHTS) --fix

ide-helper: ## generate ide-helper files
	$(SAIL) $(ARTISAN) ide-helper:generate
	$(SAIL) $(ARTISAN) ide-helper:models --nowrite
	$(SAIL) $(ARTISAN) ide-helper:meta

db-up: ## run migration and seed
	$(SAIL) $(ARTISAN) migrate --seed

db-reset: ## reset and re-seed
	$(SAIL) $(ARTISAN) migrate:refresh --seed

key-gen: ## Generate Private/Public keys
	$(SAIL) $(ARTISAN) key:generate

cache-clear: ## reset and re-seed
	$(SAIL) $(ARTISAN) cache:clear

copy-env: ## Copy .env file
	cp .env.example .env

init: init-install start key-gen db-up ## Initialize for first time setup

queue-restart:
	$(SAIL) $(ARTISAN) queue:restart