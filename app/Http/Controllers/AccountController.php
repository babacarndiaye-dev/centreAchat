<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);

        return view('account.index', compact('orders'));
    }
}
