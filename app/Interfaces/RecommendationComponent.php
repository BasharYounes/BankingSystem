<?php

namespace App\Interfaces;

interface RecommendationComponent
{
    public function getTitle(): string;
    public function getMessage(): string;
    public function getAction(): string;
}
