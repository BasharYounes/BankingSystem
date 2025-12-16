<?php

namespace App\Services\Recommendations;

use App\Interfaces\Observer;
use App\Models\AccountModel;
use App\Recommendations\Decorators\{
    BehaviorInsightDecorator,
    RiskLevelDecorator,
    PriorityDecorator,
    PersonalToneDecorator
};
use App\Recommendations\RecommendationFactory;
use App\Recommendations\TransactionAnalyzer;

class RecommendationService
{
    public function __construct(
        protected TransactionAnalyzer $analyzer,
        protected RecommendationFactory $factory
    ) {}

    protected array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($o) => $o !== $observer
        );
    }

    public function notify(string $eventType, array $data = []): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($eventType, $data['account'], $data);
        }
    }

    public function generate(AccountModel $account)
    {
        $profile = $this->analyzer->analyze($account);

        $recommendation = $this->factory->create($profile);

        // === Decorator Pipeline ===
        $recommendation = new PersonalToneDecorator($recommendation);

        if ($profile->monthlyWithdrawals > 5) {
            $recommendation = new BehaviorInsightDecorator($recommendation);

            $this->notify('recommendation.generated', [
            'account' => $account,
            'recommendation' => $recommendation,
        ]);
        }

        if ($profile->riskLevel === 'high') {
            $recommendation = new RiskLevelDecorator($recommendation);
            $recommendation = new PriorityDecorator($recommendation);
            
            $this->notify('recommendation.generated', [
            'account' => $account,
            'recommendation' => $recommendation,
        ]);
        }


        return $recommendation;
    }
}
