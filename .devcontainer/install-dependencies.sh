#!/bin/bash

if [ -f "composer.json" ]; then
    echo "Found composer.json in project root. Installing dependencies..."
    composer install --no-interaction --ignore-platform-reqs
    echo "✅ PHP dependencies installed successfully in project root."
else
    echo "⚠️ No composer.json in project root. Searching for composer.json files in subdirectories..."

    find . -type f -name "composer.json" | while read composer_file; do
        dir=$(dirname "$composer_file")
        echo "Installing dependencies in $dir..."
        (cd "$dir" && composer install --no-interaction --ignore-platform-reqs)
        if [ $? -eq 0 ]; then
            echo "✅ Dependencies installed successfully in $dir."
        else
            echo "❌ Failed to install dependencies in $dir."
        fi
    done
fi