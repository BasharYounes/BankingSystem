<?php

namespace App\Recommendations;

use App\Interfaces\RecommendationComponent;

class BaseRecommendation implements RecommendationComponent
{
    public function __construct(
        protected string $title,
        protected string $message,
        protected string $action
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}

