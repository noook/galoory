help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Creates the docker-compose stack.
	docker-compose --project-name galoory up -d

build: ## Creates the docker-compose stack.
	docker-compose --project-name galoory up -d --build

down: ## Deletes the docker-compose stack.  Your local environment will no longer be accessible.
	docker-compose --project-name galoory down

npm-install: ## Install symfony vendors
	docker-compose -f docker-compose.builder.yml run --rm install-node

composer-install: ## Install symfony vendors
	docker-compose -f docker-compose.builder.yml run --rm install-php

init: ## Initialise local environment
	$(MAKE) up
	$(MAKE) npm-install
	$(MAKE) composer-install
	$(MAKE) down
