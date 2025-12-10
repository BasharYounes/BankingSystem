<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;;
use App\Models\User;
use App\Models\AccountModel;
use App\Models\Transaction;



    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->prefix('user')->group(function () {

            Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

            Route::get('accounts-user', function (Request $request) {
               return response()->json(['User Accounts' => auth()->user()->accounts()]);
            });

            Route::post('/open-account', [App\Http\Controllers\BankController::class, 'openAccount']);
            Route::post('/deposit', [App\Http\Controllers\BankController::class, 'deposit']);
            Route::post('/withdrawal', [App\Http\Controllers\BankController::class, 'withdrawal']);
            Route::post('/transfer', [App\Http\Controllers\BankController::class, 'transfer']);
            Route::post('/add-children', [App\Http\Controllers\BankController::class, 'addChildren']);
            Route::post('/remove-children', [App\Http\Controllers\BankController::class, 'removeChildren']);
            // Route::post('/buy-stocks', [App\Http\Controllers\BankController::class, 'buyStocks']);
            Route::post('/sell-stocks', [App\Http\Controllers\BankController::class, 'sellStocks']);
        }
    );






















// Route::post('/test', function (Request $request) {
//     $user = User::first();
// //     $savingsAccount = $user->openAccount('savings', [
// //         'balance' => 5000,
// //         'interest_rate' => 2.5,
// //         'minimum_balance' => 100,
// //     ]);

// //     $deposit = $user->requestDeposit($savingsAccount, 1000, 'مرتب');
// //    ($result = $deposit->execute()?true:false);

// //    if (!$result) {
// //        return response()->json([
// //            'message' => 'فشل تنفيذ الإيداع',
// //            'account_balance' => $savingsAccount->balance,
// //            'deposit' => $deposit,
// //            'result' => $result,
// //        ], 500);
// //    }

// //     return response()->json([
// //         'message' => 'تم تنفيذ الإيداع بنجاح',
// //         'account_balance' => $savingsAccount->balance,
// //         'deposit' => $deposit,
// //         'result' => $result,
// //     ]);

// //     $checkingAccount = $user->accounts()->where('type', 'checking')->first();

// //     $withdrawal = $user->requestWithdrawal($checkingAccount, 500, 'سحب نقدي');
// //     ($result = $withdrawal->execute()?true:false);

// //    if (!$result) {
// //        return response()->json([
// //            'message' => 'فشل تنفيذ السحب',
// //            'account_balance' => $checkingAccount->balance,
// //            'withdrawal' => $withdrawal,
// //            'result' => $result,
// //        ], 500);
// //    }

// //     return response()->json([
// //         'message' => 'تم تنفيذ السحب بنجاح',
// //         'account_balance' => $checkingAccount->balance,
// //         'withdrawal' => $withdrawal,
// //         'result' => $result,
// //     ]);

//     $savingsAccount = $user->accounts()->where('type', 'savings')->first();
//     $checkingAccount = $user->accounts()->where('type', 'checking')->first();
//     $transfer = $user->requestTransfer($savingsAccount, $checkingAccount, 300, 'تحويل شخصي');
//     ($result = $transfer->execute()?true:false);
//     if (!$result) {
//          return response()->json([
//               'message' => 'فشل تنفيذ التحويل',
//               'from_account_balance' => $savingsAccount->balance,
//               'to_account_balance' => $checkingAccount->balance,
//               'transfer' => $transfer,
//               'result' => $result,
//          ], 500);
//     }

//     return response()->json([
//         'message' => 'تم تنفيذ التحويل بنجاح',
//         'from_account_balance' => $savingsAccount->balance,
//         'to_account_balance' => $checkingAccount->balance,
//         'transfer' => $transfer,
//         'result' => $result,
//     ]);
// });
