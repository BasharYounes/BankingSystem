<?php

namespace App\Console\Commands;

use App\Events\GenericNotificationEvent;
use App\Models\User;
use DB;
use Illuminate\Console\Command;

class ProcessScheduledTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = DB::table('transactions')
            ->where('status', 'pending')
            ->where('scheduled_for', '<=', now())
            ->get();

        foreach ($transactions as $transaction) {
            event(new GenericNotificationEvent(
                User::where('id',$transaction->user_id)->firstOrFail(),
                'alert for loanAccount',
                []
            ));
        }

    }
}
