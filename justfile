format:
    ./vendor/bin/pint

format-blade:
    npx blade-formatter --write --wrap=120 modules/**/*.blade.php
analyse:
    ./vendor/bin/phpstan analyse -v
test:
    php artisan test
coverage:
    XDEBUG_MODE=coverage php artisan test --coverage --coverage-html=build/html
