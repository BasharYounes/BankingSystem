<?php

namespace App\Services\LoanAccount;

use App\Models\AccountModel;
use App\Services\AccountService;
use App\Strategies\LoanAccountStrategy;


class LoanService
{
    public  function calculateLoanInterest($date, AccountModel $accountModel): float
    {
        $currentDate = now();
        $diffInMonths = $date->diffInMonths($currentDate);
        // if ($diffInMonths < 1) {
        //     throw new \Exception('لا يمكن سداد القرض قبل مرور شهر من تاريخ القرض');
        // }
        $interest = $accountModel->loan_amount * ($accountModel->interest_rate / 365) * $diffInMonths ;
        return $interest;
    }
}
