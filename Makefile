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

vapid_keys: config/vapid/public_key.txt config/vapid/private_key.txt

config/vapid:
	mkdir -p config/vapid

config/vapid/private_key.pem: config/vapid
	openssl ecparam -genkey -name prime256v1 -out config/vapid/private_key.pem

config/vapid/public_key.txt: config/vapid/private_key.pem
	openssl ec -in config/vapid/private_key.pem -pubout -outform DER | \
		tail -c 65 | \
		base64 | \
		tr -d '=' | \
		tr '/+' '_-' | \
		tr -d '\n' \
			> config/vapid/public_key.txt

config/vapid/private_key.txt: config/vapid/private_key.pem
	openssl ec -in config/vapid/private_key.pem -outform DER | \
		tail -c +8 | \
		head -c 32 | \
		base64 | \
		tr -d '=' | \
		tr '/+' '_-' \
			> config/vapid/private_key.txt
