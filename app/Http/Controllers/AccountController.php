<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }
}
