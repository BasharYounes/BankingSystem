<?php

namespace App\Recommendations;

use App\Recommendations\TP\TransactionProfile;
use App\Interfaces\RecommendationComponent;

class RecommendationFactory
{
    public function create(TransactionProfile $profile): RecommendationComponent
    {
        if ($profile->monthlyWithdrawals > 5) {
            return new BaseRecommendation(
                'إدارة السحب',
                'نقترح تقليل عدد عمليات السحب الشهرية.',
                'عرض نصائح التوفير'
            );
        }

        return new BaseRecommendation(
            'فرصة ادخار',
            'نقترح فتح حساب توفير للاستفادة من الفوائد.',
            'فتح حساب توفير'
        );
    }
}
