help: ## display this help message
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## Build the docker images
	mkcert -cert-file ./docker/nginx/localhost.pem -key-file ./docker/nginx/localhost-key.pem localhost 127.0.0.1 ::1
	docker-compose build

reset: ## Reset (or create) the database
	docker-compose exec php composer reset

db: ## Opens a Postgres console
	TERM=xterm docker-compose exec database psql -Uheimdall heimdall

psalm:
	docker-compose exec php ./vendor/bin/psalm

assets_serve: ## Serve the assets with HMR
	docker-compose exec php yarn run dev-server

assets_watch: ## Build the assets and watch them. No HMR
	docker-compose exec php node ./node_modules/.bin/encore dev --watch

assets_build: ## Build production assets
	docker-compose exec php node ./node_modules/.bin/encore production

