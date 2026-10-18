<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['message' => 'Merci !']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'service' => ['required', Rule::in(array_keys(ContactMessage::SERVICES))],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::create($data);

        // Le message est déjà enregistré : un échec d'envoi d'e-mail ne doit pas bloquer le visiteur.
        try {
            Mail::raw(
                "Nouveau message depuis le site\n\n"
                ."Nom : {$message->name}\nE-mail : {$message->email}\nEntreprise : {$message->company}\n"
                ."Service : {$message->service_label}\nSujet : {$message->subject}\n\n{$message->message}",
                fn ($mail) => $mail->to(config('studio.email'))
                    ->replyTo($message->email, $message->name)
                    ->subject('[Site] '.$message->subject),
            );
        } catch (Throwable $e) {
            Log::warning('Envoi du message de contact impossible : '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Votre message a bien été envoyé ! Nous vous répondrons sous 24 h.',
        ]);
    }
}
