<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\AccountPolicy;
use App\Services\ReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionProcessor;
use Maatwebsite\Excel\Excel;
use App\Exports\DailyTransactionsExport;

class AdminController extends Controller
{
    use AuthorizesRequests;

    protected $transactionProcessor;

    public function __construct(protected ReportService $reportService)
    {}


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


    public function addAccount(Request $request)
    {
        $policy = new AccountPolicy();
        if (!$policy->addAccount(auth()->user())) {
            abort(403, 'This action is unauthorized.');
        }
        $request->validate([
            'role' => 'required|in:manager,teller',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }


    public function getTicket()
    {
        $policy = new AccountPolicy();
        if (!$policy->getTicket(auth()->user())) {
            abort(403, 'This action is unauthorized.');
        }

        $tickets = Ticket::with('messages')->get();
        $unreadCount = 0;
        foreach ($tickets as $ticket) {
            $unreadCount += $ticket->messages()->where('is_read', false)->count();
        }

        return response()->json([
            'success' => true,
            'tickets' => $tickets,
            'unread' => $unreadCount,
        ]);
    }

    public function openTicket(Request $request,$id)
    {
        $policy = new AccountPolicy();
        if (!$policy->openTicket(auth()->user())) {
            abort(403, 'This action is unauthorized.');
        }
        $ticket = Ticket::with('messages')->where('id', $id)->firstOrFail();
        $ticket->update(['status' => 'open']);
        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    public function changeStatusTicket(Request $request,$id)
    {
        $policy = new AccountPolicy();
        if (!$policy->changeStatusTicket(auth()->user())) {
            abort(403, 'This action is unauthorized.');
        }
        $ticket = Ticket::where('id', $id)->firstOrFail();
        $ticket->update(['status' => $request->status]);
        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    public function frozenAccount($id)
    {
        $policy = new AccountPolicy();
        if (!$policy->frozenAccount(auth()->user())) {
            abort(403, 'This action is unauthorized.');
        }
        $account =  AccountModel::where('id', $id)->firstOrFail();
        $account->update(['status' => 'frozen']);
        return response()->json([
            'success' => true,
            'user' => $account
        ]);
    }

    public function downloadDailyReportExcel()
    {
        $url = $this->reportService->generateDailyTransactionsExcel();

        return response()->json([
            'excel_url' => $url
        ]);
    }

    public function getDailyTransaction()
    {
        $transactions = $this->reportService->getDailyTransactions();

        return response()->json([
            'transactions' => $transactions
        ]);
    }

    public function downloadDailyReportPdf() // donlowd
    {
        $url = $this->reportService->getDailyTransactions();

        return response()->json([
            'pdf_url' => $url
        ]);
    }
}
