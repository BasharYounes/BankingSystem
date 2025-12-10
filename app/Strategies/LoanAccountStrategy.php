<?php

namespace App\Strategies;

use App\Interfaces\StrategyTransaction;
use App\Models\AccountModel;
use App\Services\LoanAccount\LoanService;
use DB;

class LoanAccountStrategy extends AccountStrategy
{
    protected AccountModel $accountModel;
    protected float $amount;

    private float $interest;

    public function __construct(AccountModel $accountModel, float $amount, protected LoanService $loanService)
    {
        $this->accountModel = $accountModel;
        $this->amount = $amount;
    }

    public function deposit(): bool
    {
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();

            $transactionLoan = DB::table('transactions')->where('type', 'loan')->where('status', 'completed')->latest()->first();
            if (!$transactionLoan) {
                // dd($this->accountModel);
                $this->interest = $this->loanService->calculateLoanInterest($this->accountModel->created_at, $this->accountModel);
            }
            else {
                $this->interest = $this->loanService->calculateLoanInterest($transactionLoan->created_at, $this->accountModel);
            }

            $totalDue = $this->accountModel->loan_amount - $this->interest;
            if ($this->amount < $totalDue) {
                throw new \Exception('المبلغ المدفوع أقل من المبلغ المستحق للقرض مع الفوائد');
            }

            $this->updateLoanAmount($this->accountModel, $this->getLoanAmount() - ($this->amount - $this->interest));

            $this->validateLoanStatus();

            return true;

        } catch (\Exception $e) {
            throw new \Exception('فشل الإيداع: ' . $e->getMessage());
        }
    }
    public function withdraw(): bool
    {
        $this->validateAmount($this->amount);
        $this->validateStatus();
        $this->updateLoanAmount($this->accountModel, $this->getLoanAmount() + $this->amount);

        return true;
    }
    public function transfer(AccountModel $toAccount): bool
    {
        // $this->withdraw();
        // $this->validateTargetStatus($toAccount);
        // $this->updateBalance($toAccount, $toAccount->balance + $this->amount);
        // return true;
        return false;
    }

    public function validateLoanStatus(): void
    {
        if ($this->accountModel->loan_amount <= 0) {
           $this->accountModel->status = 'closed';
           $this->accountModel->save();
        }
    }

    public function getLoanAmount(): float
    {
        return $this->accountModel->loan_amount ?? 0.0;
    }

    public function updateLoanAmount(AccountModel $accountModel, float $newLoanBalance): void
    {
        $accountModel->loan_amount = $newLoanBalance;
        $accountModel->save();
    }
}
