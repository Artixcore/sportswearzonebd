<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        // Could store in a newsletter_subscribers table or send to Mailchimp etc.
        return redirect()->back()->with('success', 'Thanks for subscribing!');
    }
}
