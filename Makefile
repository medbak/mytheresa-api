# Define variables
DOCKER_COMPOSE = docker compose
PHP_CONTAINER = $(DOCKER_COMPOSE) exec php
SYMFONY = $(PHP_CONTAINER) bin/console

.PHONY: help start stop restart logs shell test cs-fix

help: ## Shows this help message
	@echo 'Usage:'
	@echo '  make [target]'
	@echo ''
	@echo 'Targets:'
	@awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")-1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  \033[32m%-15s\033[0m %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

start: ## Start the application (setup + launch)
	./scripts/start.sh

stop: ## Stop all containers
	$(DOCKER_COMPOSE) down

restart: stop start ## Restart the application

logs: ## View container logs
	$(DOCKER_COMPOSE) logs -f

shell: ## Access the PHP container shell
	$(PHP_CONTAINER) sh

test: ## Run tests
	$(PHP_CONTAINER) bin/phpunit

cs-fix: ## Fix PHP coding standards
	$(PHP_CONTAINER) vendor/bin/php-cs-fixer fix src/