.PHONY: cs-fix cs-check phpstan lint test quality ci

cs-fix:
	vendor/bin/php-cs-fixer fix

cs-check:
	vendor/bin/php-cs-fixer fix --dry-run --diff

phpstan:
	vendor/bin/phpstan analyse --memory-limit=1G

lint:
	composer validate --strict

test:
	vendor/bin/phpunit

quality: cs-check phpstan lint test

ci: quality
