SHELL := /bin/bash

test-unit:
	vendor/bin/phpunit tests/Unit

test-integration:
	symfony console doctrine:database:drop --force --env=test || true
	symfony console doctrine:database:create --env=test
	symfony console doctrine:migrations:migrate -n --env=test
	symfony console doctrine:fixtures:load -n --env=test
	vendor/bin/phpunit tests/Integration

.PHONY: test-unit test-integration
