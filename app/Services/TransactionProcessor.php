<?php

namespace App\Services;

use App\Exports\DailyTransactionsExport;
use App\Models\AccountModel;
use App\Models\Transaction;
use App\Models\TransactionRecord;
use App\Services\Recommendations\RecommendationService;
use Maatwebsite\Excel\Excel;
use Mpdf\Mpdf;

class TransactionProcessor
{
    private TransactionStrategy $strategy;
    private TransactionHandler $handlerChain;
    protected Excel $excel;

    public function __construct(Excel $excel)
    {
        // الاستراتيجية الافتراضية
        $this->strategy = new StandardTransactionStrategy();

        // بناء سلسلة المسؤولية
        $this->buildHandlerChain();
        $this->excel = $excel;
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

    public function getDailyTransactions()
{
    $today = now()->format('Y-m-d');

    $transactions= TransactionRecord::with(['account','fromAccount','toAccount','customer','approver'])->get();


    return $transactions;
}
public function generateDailyTransactionsPdf()
{
     $transactions = $this->getDailyTransactions();
    $date = now()->format('Y-m-d');

    $html = view('Reports.daily_transactions_pdf', [
        'transactions' => $transactions,
        'date' => $date
    ])->render();

    $mpdf = new Mpdf(['default_font' => 'dejavusans']);
    $mpdf->WriteHTML($html);

    $fileName = "daily_transactions_{$date}.pdf";
    $url = asset('storage/reports/' . $fileName);

    $mpdf->Output(
        storage_path('app/public/reports/' . $fileName),
        'F'
    );

    return $url;
}

      public function generateDailyTransactionsExcel()
    {
        $transactions = $this->getDailyTransactions();
        $date = now()->format('Y-m-d');

        $fileName = "daily_transactions_{$date}.xlsx";
        $path = "public/reports/{$fileName}";

        // استخدمي الكائن بدل الاستاتيك
        $this->excel->store(new DailyTransactionsExport($transactions), $path);

        return asset('storage/reports/' . $fileName);
    }
}