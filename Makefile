.PHONY: cs-fix cs-check phpstan deptrac infection lint test security-check quality ci

cs-fix:
	vendor/bin/php-cs-fixer fix

cs-check:
	vendor/bin/php-cs-fixer fix --dry-run --diff

phpstan:
	vendor/bin/phpstan analyse --memory-limit=512M

deptrac:
	vendor/bin/deptrac analyse

infection:
	vendor/bin/infection --only-covered --show-mutations --threads=max --min-msi=70

lint:
	composer validate --strict

test:
	vendor/bin/phpunit

security-check:
	composer audit --abandoned=report

quality: cs-check phpstan deptrac lint test infection

ci: security-check quality
