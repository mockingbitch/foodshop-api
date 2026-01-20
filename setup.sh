#!/bin/bash

echo "🚀 Setting up FoodShop Backend..."

# Build and start containers
echo "📦 Building Docker containers..."
docker-compose up -d --build

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 15

# Install Laravel
echo "📥 Installing Laravel..."
docker-compose exec app composer create-project --prefer-dist laravel/laravel:^10.0 .

# Copy environment file
echo "📝 Setting up environment..."
docker-compose exec app cp .env.example .env

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Install additional packages
echo "📦 Installing additional packages..."
docker-compose exec app composer require laravel/sanctum
docker-compose exec app composer require intervention/image
docker-compose exec app composer require spatie/laravel-permission

# Publish Sanctum configuration
echo "⚙️ Publishing Sanctum configuration..."
docker-compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
echo "🗄️ Running migrations..."
docker-compose exec app php artisan migrate

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage
docker-compose exec app chmod -R 775 /var/www/bootstrap/cache

echo "✅ Setup complete!"
echo "🌐 API is running at: http://localhost:8080"
echo "🗄️ phpMyAdmin is running at: http://localhost:8081"
echo ""
echo "Next steps:"
echo "1. Run: docker-compose exec app php artisan migrate --seed"
echo "2. Visit: http://localhost:8080/api"
