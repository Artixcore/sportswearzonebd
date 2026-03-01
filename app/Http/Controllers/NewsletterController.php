<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterSubscribeRequest;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterSubscribeRequest $request): RedirectResponse
    {
        $request->validated();
        // Could store in a newsletter_subscribers table or send to Mailchimp etc.
        return redirect()->back()->with('success', 'Thanks for subscribing!');
    }
}
