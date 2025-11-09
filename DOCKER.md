# Docker Setup for SPMB Laravel Application

This document provides instructions for running the SPMB application using Docker.

## Prerequisites

- Docker Engine (20.10+)
- Docker Compose (2.0+)

## Quick Start

1. **Copy the environment file:**
   ```bash
   cp .env.docker.example .env
   ```

2. **Generate application key:**
   ```bash
   docker-compose run --rm app php artisan key:generate
   ```

3. **Build and start the containers:**
   ```bash
   docker-compose up -d
   ```

4. **Run database migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Access the application:**
   Open your browser and navigate to `http://localhost:8000`

## Services

The Docker Compose setup includes the following services:

### App Service
- **Container Name:** `spmb-app`
- **Port:** 8000 (mapped to container port 80)
- **Components:**
  - PHP 8.2 FPM
  - Nginx web server
  - Supervisor (manages PHP-FPM, Nginx, and queue workers)
- **Volumes:**
  - `./storage` - Application storage (mounted for development)
  - `./bootstrap/cache` - Bootstrap cache (mounted for development)

### MySQL Service
- **Container Name:** `spmb-mysql`
- **Port:** 3306
- **Database:** spmb
- **Default Credentials:**
  - Root Password: secret
  - Username: spmb_user
  - Password: secret
- **Volume:** `mysql-data` - Persistent database storage

## Common Commands

### Start containers
```bash
docker-compose up -d
```

### Stop containers
```bash
docker-compose down
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql
```

### Execute Artisan commands
```bash
docker-compose exec app php artisan <command>
```

### Access container shell
```bash
# App container
docker-compose exec app sh

# MySQL container
docker-compose exec mysql bash
```

### Rebuild containers
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Configuration

### Environment Variables

The Docker setup uses environment variables from the `.env` file. Key variables for Docker:

- `DB_HOST=mysql` - MySQL service hostname
- `DB_PORT=3306` - MySQL port
- `DB_DATABASE=spmb` - Database name
- `DB_USERNAME=spmb_user` - Database username
- `DB_PASSWORD=secret` - Database password
- `APP_URL=http://localhost:8000` - Application URL

### Volumes

#### Named Volumes
- `mysql-data` - Persists MySQL database data

#### Bind Mounts (Development)
- `./storage` - Allows real-time file updates during development
- `./bootstrap/cache` - Allows cache clearing from host

## Troubleshooting

### Permission Issues
If you encounter permission issues with storage or cache directories:
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

### Database Connection Issues
Ensure the MySQL container is healthy:
```bash
docker-compose ps
```

If MySQL is not healthy, check logs:
```bash
docker-compose logs mysql
```

### Rebuilding Assets
If you need to rebuild frontend assets:
```bash
docker-compose exec app npm run build
```

## Production Deployment

For production use:

1. Update `.env` with production values:
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Use strong passwords
   - Configure proper `APP_URL`

2. Consider using external managed database instead of containerized MySQL

3. Add SSL/TLS termination (reverse proxy like Nginx or Traefik)

4. Implement proper backup strategy for `mysql-data` volume

5. Use Docker secrets or environment variable management tools for sensitive data

## Additional Notes

- The Dockerfile builds a production-ready image with optimized dependencies
- Supervisor manages multiple processes (PHP-FPM, Nginx, Queue workers)
- The setup includes a queue worker that runs automatically
- Healthcheck ensures MySQL is ready before the app container starts
