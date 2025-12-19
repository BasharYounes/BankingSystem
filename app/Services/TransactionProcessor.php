<?php

namespace App\Services;

use App\Exports\DailyTransactionsExport;
use App\Models\AccountModel;
use App\Models\Transaction;
use App\Models\TransactionRecord;
use App\Services\Recommendations\RecommendationService;
// use Maatwebsite\Excel\Excel;
use Mpdf\Mpdf;
use Vtiful\Kernel\Excel;

class TransactionProcessor
{
    private TransactionStrategy $strategy;
    private TransactionHandler $handlerChain;

    public function __construct()
    {
        $this->strategy = new StandardTransactionStrategy();

        $this->buildHandlerChain();
    }


    private function buildHandlerChain(): void
    {
        $validationHandler = new ValidationHandler();
        $authorizationHandler = new AuthorizationHandler();

        $validationHandler->setNext($authorizationHandler);
        $this->handlerChain = $validationHandler;
    }

    public function process(Transaction $transaction): array
    {
            try {
            if (!$this->handlerChain) {
                throw new \LogicException('Handler chain not set');
            }

            if (!$this->strategy) {
                throw new \LogicException('Strategy not set');
            }

            if (!$this->handlerChain->handle($transaction)) {
                return [
                    'success' => false,
                    'reason' => 'validation_failed',
                ];
            }


                $this->strategy->process($transaction);

                $result = $transaction->execute();

                $account = AccountModel::findOrFail($transaction->from_account_id);

                if ($account) {
                    app(RecommendationService::class)->generate($account);
                }

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'تمت المعاملة بنجاح',
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'فشل تنفيذ المعاملة',
                    'transaction_id' => $transaction->transaction_id,
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage().' خطأ في معالجة المعاملة',
                'transaction_id' => $transaction->transaction_id,
            ];
        }
    }

    public function setStrategy(TransactionStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function setHandlerChain(TransactionHandler $handlerChain): void
    {
        $this->handlerChain = $handlerChain;
    }

}
