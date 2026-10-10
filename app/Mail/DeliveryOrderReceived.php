<?php

namespace App\Mail;

use App\Models\DeliveryOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryOrderReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DeliveryOrder $deliveryOrder)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle commande à livrer - BISIKA');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.delivery-order-received');
    }
}