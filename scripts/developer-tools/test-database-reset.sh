#!/bin/bash

php8.1 bin/console cache:clear --env=test
php8.1 bin/console doctrine:database:drop --force --env=test
php8.1 bin/console doctrine:database:create --env=test
php8.1 bin/console doctrine:schema:update --force --env=test
php8.1 bin/console hautelook:fixtures:load --no-interaction --env=test
