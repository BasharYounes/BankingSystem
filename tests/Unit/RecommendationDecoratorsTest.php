<?php

namespace Tests\Unit\Recommendations;

use App\Recommendations\BaseRecommendation;
use App\Recommendations\Decorators\{
    BehaviorInsightDecorator,
    PersonalToneDecorator,
    PriorityDecorator,
    RiskLevelDecorator
};
use Tests\TestCase;

class RecommendationDecoratorsTest extends TestCase
{
    public function test_behavior_insight_decorator_adds_message()
    {
        $base = new BaseRecommendation('Title', 'Message', 'Action');

        $decorated = new BehaviorInsightDecorator($base);

        $this->assertStringContainsString(
            'عمليات سحب متكررة',
            $decorated->getMessage()
        );
    }

    public function test_personal_tone_decorator_adds_greeting()
    {
        $base = new BaseRecommendation('Title', 'Message', 'Action');

        $decorated = new PersonalToneDecorator($base);

        $this->assertStringStartsWith(
            'عزيزي العميل',
            $decorated->getMessage()
        );
    }

    public function test_priority_decorator_changes_title()
    {
        $base = new BaseRecommendation('تنبيه', 'Message', 'Action');

        $decorated = new PriorityDecorator($base);

        $this->assertStringStartsWith(
            '🚨 عاجل',
            $decorated->getTitle()
        );
    }

    public function test_risk_level_decorator_adds_warning()
    {
        $base = new BaseRecommendation('Title', 'Message', 'Action');

        $decorated = new RiskLevelDecorator($base);

        $this->assertStringContainsString(
            'مستوى المخاطرة',
            $decorated->getMessage()
        );
    }
}
