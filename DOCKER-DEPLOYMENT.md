# Docker Deployment Guide

## Prerequisites
- Docker and Docker Compose installed on your system
- Git (for cloning the repository)

## Quick Start

1. **Clone the repository** (if you haven't already):
   ```bash
   git clone <your-repository-url>
   cd rvtgtrbackup
   ```

2. **Set up the environment**:
   ```bash
   # Copy the example .env file
   cp backend/.env.example backend/.env
   
   # Update the .env file with your database credentials
   # Make sure to set:
   # DB_HOST=db
   # REDIS_HOST=redis
   # and other necessary configurations
   ```

3. **Build and start the containers**:
   ```bash
   docker-compose up -d --build
   ```

4. **Run the setup script**:
   ```bash
   # Make the script executable
   chmod +x prepare-docker-env.sh
   
   # Run the setup script
   ./prepare-docker-env.sh
   ```

5. **Install dependencies and set up the application**:
   ```bash
   # Install PHP dependencies
   docker-compose exec app composer install
   
   # Generate application key
   docker-compose exec app php artisan key:generate
   
   # Run database migrations
   docker-compose exec app php artisan migrate --seed
   
   # Generate Passport keys
   docker-compose exec app php artisan passport:keys
   
   # Create a personal access client
   docker-compose exec app php artisan passport:client --personal --name="Personal Access Client"
   
   # Install frontend dependencies
   docker-compose exec node npm install
   
   # Build frontend assets
   docker-compose exec node npm run build
   ```

6. **Set proper permissions**:
   ```bash
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

## Accessing the Application

- Frontend: http://localhost:5173
- Backend API: http://localhost/api
- MySQL: Accessible on port 3306 within the Docker network
- Redis: Accessible on port 6379 within the Docker network

## Updating the Application

To update your application:

```bash
git pull
docker-compose down
docker-compose up -d --build
docker-compose exec app php artisan migrate --force
docker-compose exec node npm run build
```

## Important Notes

1. **Environment Variables**:
   - The application will use the `.env` file in the `backend` directory
   - Make sure to set `APP_URL` to your domain or IP address
   - Update database credentials as needed

2. **Security**:
   - Change all default passwords in the `.env` file
   - Consider setting up SSL with Let's Encrypt for production

3. **Backups**:
   - Regular database backups are recommended
   - The database data is stored in a Docker volume named `dbdata`
