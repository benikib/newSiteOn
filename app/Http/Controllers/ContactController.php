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
        Mail::raw(
            "Nom : {$request->name}\nEmail : {$request->email}\nSujet : {$request->subject}\n\nMessage : \n{$request->message}",
            function ($msg) use ($request) {
                $msg->to('contactbisika@gmail.com')
                    ->subject('Nouveau message de contact');
            }
        );

        // Retour
        return back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}

