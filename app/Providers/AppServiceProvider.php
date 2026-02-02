<?php

namespace App\Providers;

use App\Contracts\Repositories\CountryRepositoryInterface;
use App\Contracts\Repositories\ExchangeRateRepositoryInterface;
use App\Contracts\Repositories\FoodCategoryRepositoryInterface;
use App\Contracts\Repositories\FoodItemRepositoryInterface;
use App\Contracts\Repositories\LanguageRepositoryInterface;
use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Contracts\Repositories\RestaurantTypeRepositoryInterface;
use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\CountryRepository;
use App\Repositories\ExchangeRateRepository;
use App\Repositories\FoodCategoryRepository;
use App\Repositories\FoodItemRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\MenuRepository;
use App\Repositories\NewsRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\RestaurantTypeRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RestaurantRepositoryInterface::class, RestaurantRepository::class);
        $this->app->bind(FoodItemRepositoryInterface::class, FoodItemRepository::class);
        $this->app->bind(FoodCategoryRepositoryInterface::class, FoodCategoryRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(ExchangeRateRepositoryInterface::class, ExchangeRateRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(RestaurantTypeRepositoryInterface::class, RestaurantTypeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
