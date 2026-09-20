<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Inertia\Inertia;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(30)->through(fn (NewsletterSubscriber $subscriber) => [
            'id' => $subscriber->id,
            'email' => $subscriber->email,
            'subscribed_at' => optional($subscriber->subscribed_at)->format('d/m/Y'),
        ]);

        return Inertia::render('Admin/Newsletter/Index', ['subscribers' => $subscribers]);
    }
}
