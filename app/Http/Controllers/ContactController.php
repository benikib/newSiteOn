<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'required',
        ]);

        // Envoyer l’email (optionnel)
        Mail::send('emails.contact', [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'messageContent' => $request->message,
        ], function ($message) use ($request) {
            $message->to('contactbisika@gmail.com')
                    ->subject('Nouveau message de contact : ' . $request->subject);
        });

        // Retour
        return back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}

