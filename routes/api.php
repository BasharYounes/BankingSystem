<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TicketController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->prefix('user')->group(function () {

            Route::post('/logout', [AuthController::class, 'logout']);

            Route::get('accounts-user', [BankController::class, 'getAccounts']);
            Route::post('search-accounts', [BankController::class, 'searchAccountsByAccountNumber']);

            Route::post('/open-account', [BankController::class, 'openAccount']);
            Route::post('/deposit', [BankController::class, 'deposit']);
            Route::post('/withdrawal', [BankController::class, 'withdrawal']);
            Route::post('/transfer', [BankController::class, 'transfer']);
            Route::post('/add-children', [BankController::class, 'addChildren']);
            Route::post('/remove-children', [BankController::class, 'removeChildren']);
            // Route::post('/buy-stocks', [App\Http\Controllers\BankController::class, 'buyStocks']);
            Route::post('/sell-stocks', [BankController::class, 'sellStocks']);

            Route::post('store-fcm-token', [AuthController::class, 'storeFCM_Token']);

            Route::post('open-ticket', [TicketController::class, 'store']);
            Route::post('add-message/{ticketId}', [TicketController::class, 'addMessage']);
            Route::post('update-status/{ticketId}', [TicketController::class, 'updateStatus']);
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
