<?php

namespace Tests\Feature;

use App\Models\DepositTransaction;
use App\Models\TransferTransaction;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\TransactionProcessor;
use App\Models\AccountModel;
use App\Models\Transaction;
use App\Services\StandardTransactionStrategy;
use App\Services\ValidationHandler;

class TransactionProcessorIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function transaction_is_processed_successfully_end_to_end()
    {

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

      $account = AccountModel::create([
        'balance' => 1000,
        'currency' => 'USD',
        'user_id' => $user->id,
        'account_number' => '1234567890',
        'type' => 'savings',
        'status' => 'active',
        'opening_date' => now(),

        ]);

        $transaction = DepositTransaction::create([
            'type' => 'deposit',
            'amount' => 100,
            'account_id' => $account->id,
            'user_id' => $user->id
        ]);

        $processor = new TransactionProcessor();

        $processor->setStrategy(new StandardTransactionStrategy());
        $processor->setHandlerChain(new ValidationHandler());

        $result = $processor->process($transaction);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_COMPLETED,
        ]);


    }
}
