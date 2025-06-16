#!/bin/bash

# Copy the example .env file if .env doesn't exist
if [ ! -f "backend/.env" ]; then
    cp backend/.env.example backend/.env
    
    # Update necessary Docker-specific settings
    sed -i 's/DB_HOST=.*/DB_HOST=db/' backend/.env
    sed -i 's/REDIS_HOST=.*/REDIS_HOST=redis/' backend/.env
    
    # Generate a new application key
    docker-compose exec app php artisan key:generate
    
    echo "Created and configured .env file for Docker"
else
    echo ".env file already exists, skipping creation"
fi
