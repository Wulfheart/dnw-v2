format:
    ./vendor/bin/pint
analyse:
    ./vendor/bin/phpstan analyse
test:
    php artisan test
coverage:
    XDEBUG_MODE=coverage php artisan test --coverage --coverage-html=build/html
