<?php

namespace App\Providers;

use App\Models\AccountModel;
use App\Observers\EmailNotificationObserver;
use App\Recommendations\RecommendationFactory;
use App\Recommendations\TransactionAnalyzer;
use App\Services\Recommendations\RecommendationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $email = new EmailNotificationObserver();

        $accountModel = new AccountModel();
        $accountModel->attach($email);

        $recommendationService = new RecommendationService(
            new TransactionAnalyzer(),
            new RecommendationFactory()
        );

        $recommendationService->attach($email);
    }
}
