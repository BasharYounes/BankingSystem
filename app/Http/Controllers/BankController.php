<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\Customer;
use App\Models\Account;
use App\Services\BankService;
use App\Strategies\CompositeAccountStrategy;
use App\Rules\CompositeAccountRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BankController extends Controller
{

    public function __construct(protected BankService $bankService)
    {}

    public function openAccount(Request $request)
    {
        $request->validate([
            'type' => 'required|in:savings,checking,loan,investment,composite',
            'balance' => 'sometimes|numeric|min:0',
        ]);

        $user = Auth::user();

        $account = $this->bankService->createAccount(
            $user,
            $request->type,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'تم فتح الحساب بنجاح',
            'account' => $account,
        ]);
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $account = AccountModel::where('id', $request->account_id)->firstOrFail();

        if (!$account) {
            return response()->json(['error' => 'الحساب غير موجود'], 404);
        }

        $transaction = $user->requestDeposit(
            $account,
            $request->amount,
            $request->description ?? 'إيداع نقدي'
        );

        $result = $this->bankService->processTransaction($transaction);

        return response()->json($result);
    }

    public function withdrawal(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $account = AccountModel::where('id', $request->account_id)->firstOrFail();

        if (!$account) {
            return response()->json(['error' => 'الحساب غير موجود'], 404);
        }

        $transaction = $user->requestWithdrawal(
            $account,
            $request->amount,
            $request->description ?? 'إيداع نقدي'
        );
        $result = $this->bankService->processTransaction($transaction)?true:false;

        return response()->json([
            'result'=>$result,
            'data' => [
                'account_balance' => $account->balance,
                'transaction' => $transaction,
            ]
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_account' => 'required|exists:accounts,id',
            'to_account' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();

        $fromAccount = AccountModel::where('id', $request->from_account)->firstOrFail();
        $toAccount = AccountModel::where('id', $request->to_account)->firstOrFail();

        if (!$fromAccount || !$toAccount) {
            return response()->json(['error' => 'الحساب غير موجود'], 404);
        }

        $transaction = $user->requestTransfer(
            $fromAccount,
            $toAccount,
            $request->amount,
            $request->description ?? "تحويل من الحساب {$fromAccount->account_number} إلى الحساب {$toAccount->account_number}"
        );

        $result = $this->bankService->processTransaction($transaction)?true:false;

        return response()->json([
            'result'=>$result,
            'data' => [
                'from_account_balance' => $fromAccount->balance,
                'to_account_balance' => $toAccount->balance,
                'transaction' => $transaction,
            ]
        ]);
    }

    public function addChildren(Request $request)
    {
        $request->validate([
            'parent_account_id' => ['required', new CompositeAccountRule()],
            'child_account_ids' => 'required|exists:accounts,id',
        ]);

        $parentAccount = AccountModel::where('id', $request->parent_account_id)->firstOrFail();

        $childAccount = AccountModel::where('id', $request->child_account_ids)->firstOrFail();

        $strategy = new CompositeAccountStrategy($parentAccount);
        $strategy->addChild($childAccount);

        return response()->json([
            'message' => 'تمت إضافة الحسابات الفرعية بنجاح',
            'parent_account' => $parentAccount,
            'children' => [$parentAccount->children()->get()],
        ]);
    }

    public function removeChildren(Request $request)
    {
        $request->validate([
            'parent_account_id' => ['required', new CompositeAccountRule()],
            'child_account_ids' => 'required|exists:accounts,id',
        ]);

        $parentAccount = AccountModel::where('id', $request->parent_account_id)->firstOrFail();

        $childAccount = AccountModel::where('id', $request->child_account_ids)->firstOrFail();

        $strategy = new CompositeAccountStrategy($parentAccount);
        $strategy->removeChild($childAccount);

        return response()->json([
            'message' => 'تمت إزالة الحسابات الفرعية بنجاح',
            'parent_account' => $parentAccount,
            'children' => [$parentAccount->children()->get()],
        ]);
    }


    public function sellStocks(Request $request)
    {
        $request->validate([
            'from_account' => 'required|exists:accounts,id',
            'to_account' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'metadata' => 'required|array', [
                'asset_symbol' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'price_per_unit' => 'required|numeric|min:0.01',
            ]
        ]);

        $user = Auth::user();

        $transaction = $user->requestSell(
            AccountModel::where('id', $request->from_account)->firstOrFail(),
            AccountModel::where('id', $request->to_account)->firstOrFail()  ,
            $request->amount,
            $request->description ?? 'بيع اسهم',
            $request->metadata
        );

        $result = $this->bankService->processTransaction($transaction);

        return response()->json([
            'message' => 'تمت الهملية بنجاح',
            'result' => $result
        ]);
    }




    public function getAccounts()
    {
        $customer = Auth::user();
        $accounts = $this->bankService->getCustomerAccounts($customer);

        $accountsData = [];
        foreach ($accounts as $account) {
            $accountsData[] = $account->display();
        }

        return response()->json([
            'accounts' => $accountsData,
            'total_balance' => collect($accountsData)->sum('balance'),
        ]);
    }
}
