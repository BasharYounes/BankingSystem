<?php

namespace App\Recommendations\Decorators;

use App\Interfaces\RecommendationComponent;

abstract class RecommendationDecorator implements RecommendationComponent
{
    public function __construct(
        protected RecommendationComponent $recommendationComponent
    ) {}

    public function getTitle(): string
    {
        return $this->recommendationComponent->getTitle();
    }

    public function getMessage(): string
    {
        return $this->recommendationComponent->getMessage();
    }

    public function getAction(): string
    {
        return $this->recommendationComponent->getAction();
    }
}
