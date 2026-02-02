<?php

namespace App\Services;

use App\Contracts\Repositories\FoodItemRepositoryInterface;
use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

/**
 * Admin dashboard statistics: counts for restaurants, food items, news, users, reviews.
 */
class AdminService
{
    public function __construct(
        protected RestaurantRepositoryInterface $restaurantRepository,
        protected FoodItemRepositoryInterface $foodItemRepository,
        protected NewsRepositoryInterface $newsRepository,
        protected UserRepositoryInterface $userRepository,
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    /**
     * Get dashboard statistics (totals and by status/type).
     *
     * @return array<string, array<string, int>>
     */
    public function getStats(): array
    {
        return [
            'restaurants' => [
                'total' => $this->restaurantRepository->count(),
                'active' => $this->restaurantRepository->countActive(),
                'pending' => $this->restaurantRepository->countPending(),
                'hidden' => $this->restaurantRepository->countHidden(),
            ],
            'food_items' => [
                'total' => $this->foodItemRepository->count(),
                'active' => $this->foodItemRepository->countActive(),
                'pending' => $this->foodItemRepository->countPending(),
                'pending_code_confirmation' => $this->foodItemRepository->countPendingCodeConfirmation(),
            ],
            'news' => [
                'total' => $this->newsRepository->count(),
                'published' => $this->newsRepository->countPublished(),
                'draft' => $this->newsRepository->countDraft(),
            ],
            'users' => [
                'total' => $this->userRepository->count(),
                'owners' => $this->userRepository->countOwners(),
                'admins' => $this->userRepository->countAdmins(),
            ],
            'reviews' => [
                'total' => $this->reviewRepository->count(),
                'pending' => $this->reviewRepository->countPending(),
                'approved' => $this->reviewRepository->countApproved(),
            ],
        ];
    }
}
