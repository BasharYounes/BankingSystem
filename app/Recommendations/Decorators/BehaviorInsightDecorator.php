<?php

namespace App\Recommendations\Decorators;

class BehaviorInsightDecorator extends RecommendationDecorator
{
    public function getMessage(): string
    {
        return parent::getMessage()
            . "\n\n📊 ملاحظة: نلاحظ عمليات سحب متكررة خلال الشهر.";
    }
}
