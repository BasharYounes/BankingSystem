<?php

namespace Tests\Unit\Recommendations;

use App\Recommendations\RecommendationFactory;
use App\Recommendations\TP\TransactionProfile;
use Tests\TestCase;

class RecommendationFactoryTest extends TestCase
{
    //==TC1 */
    public function test_factory_returns_withdrawal_management_recommendation()
    {
        $profile = new TransactionProfile(
            monthlyWithdrawals: 6,
            averageBalance: 1000,
            feesPaid: 0,
            riskLevel: 'high',
            hasRegularIncome: true
        );

        $factory = new RecommendationFactory();
        $recommendation = $factory->create($profile);

        $this->assertEquals('إدارة السحب', $recommendation->getTitle());
    }

    /** TC2 */
    public function test_factory_returns_saving_opportunity_recommendation()
    {
        $profile = new TransactionProfile(
            monthlyWithdrawals: 2,
            averageBalance: 2000,
            feesPaid: 0,
            riskLevel: 'low',
            hasRegularIncome: true
        );

        $factory = new RecommendationFactory();
        $recommendation = $factory->create($profile);

        $this->assertEquals('فرصة ادخار', $recommendation->getTitle());
    }
}
