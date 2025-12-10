<?php

namespace App\Factories;

use App\Models\AccountModel;
use App\Models\ConcreteCompositeAccount;
use App\Models\SavingsAccount;
use App\Models\CheckingAccount;
use App\Models\LoanAccount;
use App\Strategies\CheckingAccountStrategy;
use App\Strategies\CompositeAccountStrategy;
use App\Strategies\InvestmentAccountStrategy;
use App\Strategies\LoanAccountStrategy;
use App\Strategies\SavingAccountStrategy;


class StrategyFactory
{
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public static function createStrategy(AccountModel $account, float $amount): mixed
    {
        $factory = self::getInstance();
        return match ($account->type) {
            'savings'    => $factory->createSavingAccountStrategy($account, $amount),
            'checking'   => $factory->createCheckingAccountStrategy($account, $amount),
            'loan'       => $factory->createLoanAccountStrategy($account, $amount),
            'investment' => $factory->createInvestmentAccountStrategy($account, $amount),
            'composite'  => $factory->createCompositeAccountStrategy($account, $amount),
            default      => throw new \Exception('نوع حساب غير مدعوم'),
        };
    }

    private function createSavingAccountStrategy(AccountModel $account, float $amount): SavingAccountStrategy
    {
        return new SavingAccountStrategy($account, $amount);
    }
    private function createCheckingAccountStrategy(AccountModel $account, float $amount): CheckingAccountStrategy
    {
        return new CheckingAccountStrategy($account, $amount);
    }
    private function createLoanAccountStrategy(AccountModel $account, float $amount): LoanAccountStrategy
    {
        return new LoanAccountStrategy($account, $amount, new \App\Services\LoanAccount\LoanService());
    }
    private function createInvestmentAccountStrategy(AccountModel $account, float $amount): InvestmentAccountStrategy
    {
        return new InvestmentAccountStrategy($account, $amount);
    }
    private function createCompositeAccountStrategy(AccountModel $account, float $amount): CompositeAccountStrategy
    {
        return new CompositeAccountStrategy($account, $amount);
    }
}
