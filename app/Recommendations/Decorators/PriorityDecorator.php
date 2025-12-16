<?php

namespace App\Recommendations\Decorators;

class PriorityDecorator extends RecommendationDecorator
{
    public function getTitle(): string
    {
        return '🚨 عاجل: ' . parent::getTitle();
    }
}
