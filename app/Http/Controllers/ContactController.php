<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactSubmitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact');
    }

    public function submit(ContactSubmitRequest $request): RedirectResponse
    {
        $request->validated();
        // For now just flash success; can wire to Mail::later or queue
        return redirect()->route('contact')->with('success', 'Thank you. We will get back to you soon.');
    }
}
