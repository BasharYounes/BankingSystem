<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionProcessor;
use Maatwebsite\Excel\Excel; 
use App\Exports\DailyTransactionsExport;

class AdminController extends Controller
{

    protected $transactionProcessor;

    public function __construct(TransactionProcessor $transactionProcessor)
    {
        $this->transactionProcessor = $transactionProcessor;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin registered successfully',
            'access_token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = User::where('email', $request->email)->first();

        if (!$admin || !password_verify($request->password, $admin->password)) {
            $token = $admin->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Admin logged in successfully',
                'access_token' => $token,
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'User logged out successfully']);
    }

    public function getUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getDailyTransactions()
    {
         $transactions = $this->transactionProcessor->getDailyTransactions();
         return response()->json($transactions);
    
    }

    public function downloadDailyReport()
{
    $url = $this->transactionProcessor->generateDailyTransactionsPdf();

    return response()->json([
        'pdf_url' => $url
    ]);
}

public function downloadDailyReportExcel()
{
    $url = $this->transactionProcessor->generateDailyTransactionsExcel();

    return response()->json([
        'excel_url' => $url
    ]);
}
}
    