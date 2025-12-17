<?php

namespace App\Services;

use App\Models\AccountModel;
use App\Models\Transaction;
use App\Services\Recommendations\RecommendationService;

class TransactionProcessor
{
    private TransactionStrategy $strategy;
    private TransactionHandler $handlerChain;

    public function __construct()
    {
        // الاستراتيجية الافتراضية
        $this->strategy = new StandardTransactionStrategy();

        // بناء سلسلة المسؤولية
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
            // 1. التحقق من الصحة عبر Chain of Responsibility
            $isValid = $this->handlerChain->handle($transaction);

            if (!$isValid) {
                return [
                    'success' => false,
                    'message' => 'فشل التحقق من المعاملة',
                    'status' => $transaction->status,
                ];
            }

            $this->strategy->process($transaction);

            $result = $transaction->execute();

            // معالجة آمنة للتوصيات
            try {
                $account = AccountModel::find($transaction->from_account_id);
                if ($account) {
                    app(RecommendationService::class)->generate($account);
                }
            } catch (\Exception $e) {
                \Log::warning('تحذير في إنشاء التوصيات: ' . $e->getMessage());
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


}
