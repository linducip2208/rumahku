<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Property $property,
        public Lead $lead,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leads baru: '.$this->property->title,
        );
    }

    public function content(): Content
    {
        $waNumber = (string) preg_replace('/\D+/', '', (string) $this->lead->phone);

        return new Content(
            view: 'mail.lead-received',
            with: [
                'property' => $this->property,
                'lead' => $this->lead,
                'waNumber' => $waNumber,
            ],
        );
    }
}
