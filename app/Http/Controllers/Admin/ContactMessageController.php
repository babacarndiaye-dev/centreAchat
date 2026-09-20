<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20)->through(fn (ContactMessage $message) => [
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'subject' => $message->subject,
            'created_at' => $message->created_at->format('d/m/Y H:i'),
            'is_read' => $message->is_read,
            'has_reply' => (bool) $message->reply,
        ]);

        return Inertia::render('Admin/Messages/Index', ['messages' => $messages]);
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        $message->load('repliedBy');

        return Inertia::render('Admin/Messages/Show', [
            'message' => [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'phone' => $message->phone,
                'subject' => $message->subject,
                'message' => $message->message,
                'created_at' => $message->created_at->format('d/m/Y H:i'),
                'reply' => $message->reply,
                'replied_at' => $message->replied_at?->format('d/m/Y à H:i'),
                'replied_by_name' => $message->repliedBy?->name,
            ],
        ]);
    }

    public function reply(Request $request, ContactMessage $message): RedirectResponse
    {
        $data = $request->validate([
            'reply' => ['required', 'string', 'max:5000'],
        ]);

        Mail::send('emails.layout', [
            'title' => 'Réponse à votre message',
            'body' => $data['reply'],
        ], function ($mail) use ($message) {
            $mail->to($message->email, $message->name)->subject('Réponse à votre message');
        });

        $message->update([
            'reply' => $data['reply'],
            'replied_at' => now(),
            'replied_by' => Auth::id(),
        ]);

        return back()->with('success', 'Réponse envoyée à '.$message->email.'.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message supprimé.');
    }
}
