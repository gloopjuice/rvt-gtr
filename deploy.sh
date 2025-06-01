#!/bin/bash

# Update system packages
sudo apt-get update
sudo apt-get upgrade -y

# Install Docker and Docker Compose if not installed
if ! command -v docker &> /dev/null; then
    echo "Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
fi

if ! command -v docker-compose &> /dev/null; then
    echo "Installing Docker Compose..."
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

# Install Git if not installed
if ! command -v git &> /dev/null; then
    echo "Installing Git..."
    sudo apt-get install -y git
fi

# Clone the repository if not already present
if [ ! -d "rvtgtr" ]; then
    git clone https://github.com/yourusername/rvtgtr.git
    cd rvtgtr
else
    cd rvtgtr
    git pull origin main
fi

# Copy environment file
cp .env.docker .env

# Set proper permissions
sudo chown -R $USER:$USER .

# Build and start containers
docker-compose up -d --build

# Install PHP dependencies
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate --force

# Install frontend dependencies
docker-compose exec node npm install

# Build frontend assets
docker-compose exec node npm run build

# Set storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

echo "Deployment completed successfully!"
echo "Your application should be available at: http://64.227.105.115"
