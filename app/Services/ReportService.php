<?php

namespace App\Services;

use App\Exports\DailyTransactionsExport;
use App\Models\Transaction;
use App\Models\TransactionRecord;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportService
{
    public function __construct()
    {

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
        Excel::store(new DailyTransactionsExport($transactions), $path);

        return asset('storage/reports/' . $fileName);
    }
}
