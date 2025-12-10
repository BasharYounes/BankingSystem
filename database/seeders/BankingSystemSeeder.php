<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Services\BankService;

class BankingSystemSeeder extends Seeder
{
    public function run()
    {
        // 1. إنشاء عميل
        $customer = User::create([
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => '1234567890',
//            'address' => 'دمشق، سوريا',
//            'status' => 'active',
        ]);

        // 2. إنشاء حسابات
        $savingsAccount = $customer->openAccount('savings', [
//            'account_name' => 'حساب التوفير الشخصي',
            'balance' => 5000,
            'interest_rate' => 2.5,
            'minimum_balance' => 100,
        ]);

        $checkingAccount = $customer->openAccount('checking', [
//            'account_name' => 'الحساب الجاري',
            'balance' => 10000,
            'overdraft_limit' => 1000,
        ]);

        // 3. إنشاء حساب مركب (عائلي)
        $familyAccount = $customer->openAccount('composite', [
//            'account_name' => 'الحساب العائلي',
            'is_composite' => true,
        ]);

        // 4. إضافة حسابات فرعية للحساب المركب
        $childAccount1 = $customer->openAccount('savings', [
//            'account_name' => 'حساب الابن',
            'balance' => 1000,
            'parent_id' => $familyAccount->id,
//            'tree_level' => 1,
        ]);

        $childAccount2 = $customer->openAccount('savings', [
//            'account_name' => 'حساب البنت',
            'balance' => 800,
            'parent_id' => $familyAccount->id,
//            'tree_level' => 1,
        ]);

        $this->command->info('✅ تم إنشاء النظام المصرفي');
        $this->command->info('   العميل: ' . $customer->name);
        $this->command->info('   الحسابات:');
        $this->command->info('     - ' . $savingsAccount->account_number . ' (توفير): ' . $savingsAccount->balance . ' $');
        $this->command->info('     - ' . $checkingAccount->account_number . ' (جاري): ' . $checkingAccount->balance . ' $');
        $this->command->info('     - ' . $familyAccount->account_number . ' (مركب): ' . $familyAccount->balance . ' $');
        $this->command->info('       ↳ ' . $childAccount1->account_number . ' (ابن): ' . $childAccount1->balance . ' $');
        $this->command->info('       ↳ ' . $childAccount2->account_number . ' (بنت): ' . $childAccount2->balance . ' $');

        // 5. اختبار معاملات
        $this->command->info('   اختبار المعاملات:');

        // إيداع
        $deposit = $customer->requestDeposit($savingsAccount, 1000, 'مرتب');
        $this->command->info('     - إيداع 1000 في حساب التوفير: ' . ($deposit->execute() ? '✅' : '❌'));

        // سحب
        $withdrawal = $customer->requestWithdrawal($checkingAccount, 500, 'سحب نقدي');
        $this->command->info('     - سحب 500 من الحساب الجاري: ' . ($withdrawal->execute() ? '✅' : '❌'));

        // تحويل
        $transfer = $customer->requestTransfer($savingsAccount, $checkingAccount, 300, 'تحويل شخصي');
        $this->command->info('     - تحويل 300 من التوفير إلى الجاري: ' . ($transfer->execute() ? '✅' : '❌'));
    }
}
