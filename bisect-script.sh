#!/bin/bash

## Get commit hashes
CURRENT_COMMIT=$(git rev-parse HEAD)
LAST_COMMIT=$(git rev-parse HEAD~1)

## Setup previous commit
git checkout "$LAST_COMMIT"
composer install --no-interaction > /dev/null 2>&1
./bin/console doctrine:database:drop --force --no-interaction > /dev/null 2>&1
./bin/console doctrine:database:create --if-not-exists --no-interaction > /dev/null 2>&1
./bin/console doctrine:schema:drop --force > /dev/null 2>&1
./bin/console doctrine:schema:create > /dev/null 2>&1
./bin/console doctrine:migrations:version --delete --all --no-interaction > /dev/null 2>&1
./bin/console doctrine:migrations:version --add --all --no-interaction > /dev/null 2>&1

## Setup current commit
git checkout "$CURRENT_COMMIT"
composer install --no-interaction > /dev/null 2>&1

# Actual test
./bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

MIGRATION_EXIT_CODE=$?
if [ $MIGRATION_EXIT_CODE -eq 0 ]; then
    # Migration succeeded: This is a "Good" commit
    exit 0
else
    # Migration failed: This is a "Bad" commit
    exit 1
fi
