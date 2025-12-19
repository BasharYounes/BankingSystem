<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AccountModel;
use App\Strategies\SavingAccountStrategy;

class SavingAccountStrategyTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_successfully_updates_balance()
    {
        $user = User::factory()->create();
        // Arrange
        $account = AccountModel::create([
            'balance' => 1000,
            'status' => 'active',
            'type' => 'savings',
            'account_number' => '1234567890',
            'user_id' => $user->id,
            'opening_date' => now(),
        ]);

        $strategy = new SavingAccountStrategy($account, 500);

        // Act
        $result = $strategy->deposit();

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(1500, $account->fresh()->balance);
    }

    public function test_withdraw_successfully_updates_balance()
    {
        $user = User::factory()->create();
        $account = AccountModel::create([
            'balance' => 1000,
            'status' => 'active',
            'type' => 'savings',
            'account_number' => '12345678890',
            'user_id' => $user->id,
            'opening_date' => now(),
        ]);

        $strategy = new SavingAccountStrategy($account, 300);

        $result = $strategy->withdraw();

        $this->assertTrue($result);
        $this->assertEquals(700, $account->fresh()->balance);
    }

    public function test_withdraw_fails_when_monthly_limit_exceeded()
    {
        $user = User::factory()->create();

        $account = AccountModel::create([
            'balance' => 1000,
            'status' => 'active',
            'type' => 'savings',
            'account_number' => '123456788990',
            'user_id' => $user->id,
            'opening_date' => now(),
        ]);

        \DB::table('transactions')->insert([
            [
                'id' => 1,
                'transaction_id' => 1,
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'transaction_id' => 2,
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'transaction_id' => 3,
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'transaction_id' => 4,
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $strategy = new SavingAccountStrategy($account, 100);

        $this->expectException(\Exception::class);

        $strategy->withdraw();
    }

    public function test_withdraw_fails_when_account_is_not_active()
    {
        $user = User::factory()->create();

        $account = AccountModel::create([
            'balance' => 1000,
            'status' => 'frozen',
            'type' => 'savings',
            'account_number' => '1234576788990',
            'opening_date' => now(),
            'user_id' => $user->id
        ]);

        $strategy = new SavingAccountStrategy($account, 100);

        $this->expectException(\Exception::class);

        $strategy->withdraw();
    }

    public function test_transfer_moves_money_between_accounts()
    {
        $user = User::factory()->create();

        $from = AccountModel::create([
            'balance' => 1000,
            'status' => 'active',
            'user_id' => $user->id,
            'opening_date' => now(),
            'account_number' => '123457678890',
            'type' => 'savings',
        ]);

        $to = AccountModel::create([
            'balance' => 200,
            'status' => 'active',
            'user_id' => $user->id,
            'opening_date' => now(),
            'account_number' => '123457678890',
            'type' => 'checking',
        ]);

        $strategy = new SavingAccountStrategy($from, 300);

        $result = $strategy->transfer($to);

        $this->assertTrue($result);
        $this->assertEquals(700, $from->fresh()->balance);
        $this->assertEquals(500, $to->fresh()->balance);
    }

}

