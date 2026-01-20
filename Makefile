.PHONY: help install setup up down restart build clean test migrate seed fresh serve shell composer artisan cache-clear logs

# Colors for output
CYAN := \033[0;36m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m # No Color

# Default target
.DEFAULT_GOAL := help

help: ## Show this help message
	@echo "$(CYAN)Available commands:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

# ============================================================
# Installation & Setup
# ============================================================

install: ## Install composer dependencies
	@echo "$(CYAN)Installing composer dependencies...$(NC)"
	composer install

update: ## Update composer dependencies
	@echo "$(CYAN)Updating composer dependencies...$(NC)"
	composer update

setup: install env key migrate seed ## Complete setup: install, env, key, migrate, seed
	@echo "$(GREEN)Setup completed!$(NC)"

env: ## Copy .env.example to .env if not exists
	@if [ ! -f .env ]; then \
		echo "$(CYAN)Creating .env file...$(NC)"; \
		cp .env.example .env; \
	else \
		echo "$(YELLOW).env file already exists$(NC)"; \
	fi

key: ## Generate application key
	@echo "$(CYAN)Generating application key...$(NC)"
	php artisan key:generate

# ============================================================
# Docker Commands
# ============================================================

up: ## Start Docker containers
	@echo "$(CYAN)Starting Docker containers...$(NC)"
	docker-compose up -d

down: ## Stop Docker containers
	@echo "$(CYAN)Stopping Docker containers...$(NC)"
	docker-compose down

restart: down up ## Restart Docker containers
	@echo "$(GREEN)Docker containers restarted!$(NC)"

build: ## Build Docker containers
	@echo "$(CYAN)Building Docker containers...$(NC)"
	docker-compose build

rebuild: down build up ## Rebuild and restart Docker containers
	@echo "$(GREEN)Docker containers rebuilt!$(NC)"

logs: ## Show Docker logs
	docker-compose logs -f

logs-app: ## Show app container logs
	docker-compose logs -f app

logs-nginx: ## Show nginx container logs
	docker-compose logs -f nginx

logs-db: ## Show database container logs
	docker-compose logs -f db

# ============================================================
# Artisan Commands
# ============================================================

artisan: ## Run artisan command (usage: make artisan CMD="migrate")
	php artisan $(CMD)

serve: ## Start Laravel development server
	@echo "$(CYAN)Starting Laravel development server...$(NC)"
	php artisan serve

shell: ## Open shell in app container
	docker-compose exec app sh

shell-db: ## Open MySQL shell
	docker-compose exec db mysql -u foodshop_user -pfoodshop_pass foodshop_db

# ============================================================
# Database Commands
# ============================================================

migrate: ## Run database migrations
	@echo "$(CYAN)Running migrations...$(NC)"
	php artisan migrate

migrate-fresh: ## Drop all tables and re-run migrations
	@echo "$(CYAN)Fresh migration...$(NC)"
	php artisan migrate:fresh

migrate-rollback: ## Rollback the last migration
	@echo "$(CYAN)Rolling back migration...$(NC)"
	php artisan migrate:rollback

migrate-reset: ## Rollback all migrations
	@echo "$(CYAN)Resetting migrations...$(NC)"
	php artisan migrate:reset

seed: ## Run database seeders
	@echo "$(CYAN)Running seeders...$(NC)"
	php artisan db:seed

fresh: migrate-fresh seed ## Drop all tables, re-run migrations and seed
	@echo "$(GREEN)Database refreshed!$(NC)"

# ============================================================
# Cache & Optimization
# ============================================================

cache-clear: ## Clear all caches
	@echo "$(CYAN)Clearing caches...$(NC)"
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

cache: ## Cache configuration and routes
	@echo "$(CYAN)Caching configuration and routes...$(NC)"
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache

optimize: cache ## Optimize application (cache config, routes, views)
	@echo "$(GREEN)Application optimized!$(NC)"

# ============================================================
# Testing
# ============================================================

test: ## Run PHPUnit tests
	@echo "$(CYAN)Running tests...$(NC)"
	php artisan test

test-coverage: ## Run tests with coverage
	@echo "$(CYAN)Running tests with coverage...$(NC)"
	php artisan test --coverage

pint: ## Run Laravel Pint (code style fixer)
	@echo "$(CYAN)Running Laravel Pint...$(NC)"
	./vendor/bin/pint

pint-test: ## Test code style without fixing
	@echo "$(CYAN)Testing code style...$(NC)"
	./vendor/bin/pint --test

# ============================================================
# Composer Commands
# ============================================================

composer: ## Run composer command (usage: make composer CMD="require package")
	composer $(CMD)

dump-autoload: ## Regenerate composer autoload files
	@echo "$(CYAN)Regenerating autoload files...$(NC)"
	composer dump-autoload

# ============================================================
# Development Helpers
# ============================================================

tinker: ## Open Laravel Tinker
	php artisan tinker

route-list: ## List all routes
	php artisan route:list

make-controller: ## Create a new controller (usage: make make-controller NAME="UserController")
	php artisan make:controller $(NAME)

make-model: ## Create a new model (usage: make make-model NAME="User")
	php artisan make:model $(NAME)

make-migration: ## Create a new migration (usage: make make-migration NAME="create_users_table")
	php artisan make:migration $(NAME)

make-seeder: ## Create a new seeder (usage: make make-seeder NAME="UserSeeder")
	php artisan make:seeder $(NAME)

# ============================================================
# Quick Actions
# ============================================================

quick-setup: env install key migrate seed ## Quick setup for new developers
	@echo "$(GREEN)Quick setup completed!$(NC)"

dev: up cache-clear serve ## Start development environment (Docker + serve)

clean: cache-clear ## Clean all caches and temporary files
	@echo "$(CYAN)Cleaning temporary files...$(NC)"
	rm -rf storage/framework/cache/*
	rm -rf storage/framework/sessions/*
	rm -rf storage/framework/views/*
	@echo "$(GREEN)Clean completed!$(NC)"

# ============================================================
# Production
# ============================================================

deploy: install migrate optimize ## Deploy commands (install, migrate, optimize)
	@echo "$(GREEN)Deployment completed!$(NC)"
