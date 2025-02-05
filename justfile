set shell := ["zsh", "-cu"]

format: format-php


format-php:
    ./vendor/bin/pint
#format-blade:
#    npx blade-formatter --write --wrap=120 resources/**/*.blade.php
analyse:
    ./vendor/bin/phpstan analyse -v
test:
    php artisan test --parallel
coverage:
    XDEBUG_MODE=coverage php artisan test --parallel --coverage --coverage-html=build/html
dev:
    composer run dev
