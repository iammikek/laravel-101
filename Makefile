.PHONY: test serve migrate docker-up docker-down

test:
	php artisan test

serve:
	php artisan serve --host=localhost --port=8003

migrate:
	php artisan migrate --force

docker-up:
	docker compose up --build

docker-down:
	docker compose down
