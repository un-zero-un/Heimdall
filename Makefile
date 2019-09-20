help: ## display this help message
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## Build the docker images
	docker-compose build

reset: ## Reset (or create) the database
	docker-compose exec php composer reset

db: ## Opens a Postgres console
	TERM=xterm docker-compose exec database psql -Uheimdall heimdall

psalm:
	docker-compose exec php ./vendor/bin/psalm
