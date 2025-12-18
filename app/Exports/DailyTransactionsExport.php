<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DailyTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected Collection $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Transaction ID', 'Type', 'Amount', 'Fees', 'Net Amount', 'Currency',
            'Status', 'From Account', 'To Account', 'Account', 'Customer', 'Approver', 'Executed At'
        ];
    }

    public function map($txn): array
    {
        return [
            $txn->transaction_id ?? '-',
            $txn->type ?? '-',
            $txn->amount ?? 0,
            $txn->fees ?? 0,
            $txn->net_amount ?? 0,
            $txn->currency ?? '-',
            $txn->status ?? '-',
            optional($txn->fromAccount)->account_number ?? '-',
            optional($txn->toAccount)->account_number ?? '-',
            optional($txn->account)->account_number ?? 'N/A',
            optional($txn->customer)->name ?? 'N/A',
            optional($txn->approver)->name ?? '-',
            $txn->executed_at ? $txn->executed_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER, // Amount
            'D' => NumberFormat::FORMAT_NUMBER, // Fees
            'E' => NumberFormat::FORMAT_NUMBER, // Net Amount
            'M' => NumberFormat::FORMAT_DATE_DATETIME, // Executed At
        ];
    }
}
