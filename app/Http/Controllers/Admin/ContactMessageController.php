<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return view('admin.messages.show', compact('message'));
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
