<?php

namespace App\Recommendations\Decorators;

class PersonalToneDecorator extends RecommendationDecorator
{
    public function getMessage(): string
    {
        return "عزيزي العميل،\n" . parent::getMessage();
    }
}
