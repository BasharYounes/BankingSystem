<?php

namespace App\Recommendations\Decorators;

class RiskLevelDecorator extends RecommendationDecorator
{
    public function getMessage(): string
    {
        return parent::getMessage()
            . "\n⚠️ تنبيه: مستوى المخاطرة في حسابك مرتفع.";
    }
}
