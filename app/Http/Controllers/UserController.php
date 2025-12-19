<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\User;
use App\Services\BankService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserNotifications()
    {
        $user = auth()->user();
        $notifications = $user->notifications;
        return response()->json($user->notifications);
    }

    public function getChildren()
    {
        $account = AccountModel::where('id', request('id'))->first();
        $children = $account->children;
        return response()->json($children);
    }
}
