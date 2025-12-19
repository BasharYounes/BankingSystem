<?php

namespace App\Services;

use App\Exports\DailyTransactionsExport;
use App\Interfaces\TransactionContract;
use App\Models\AccountModel;
use App\Models\Transaction;
use App\Models\TransactionRecord;
use App\Services\Recommendations\RecommendationService;
use Mpdf\Mpdf;

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

    public function process(TransactionContract $transaction): array
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

                // if (!$transaction->getFromAccountId()) {
                //      return [];
                // }

                // $account = AccountModel::find($transaction->getFromAccountId());

                // if ($account) {
                //     app(RecommendationService::class)->generate($account);
                // }

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'تمت المعاملة بنجاح',
                    'transaction_id' => $transaction->getTransactionId(),
                    'amount' => $transaction->getAmount(),
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'فشل تنفيذ المعاملة',
                    'transaction_id' => $transaction->getTransactionId(),
                ];
            }

        } catch (\LogicException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'reason' => 'strategy_failed',
                'message' => $e->getMessage(),
                'transaction_id' => $transaction->getTransactionId(),
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
