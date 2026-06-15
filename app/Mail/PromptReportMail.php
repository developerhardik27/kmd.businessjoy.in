<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromptReportMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $buyerName;
    public $buyerEmail;
    public $invoices;
    public $customMessage;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($buyerName, $buyerEmail, $invoices, $customMessage, $companyName = null)
    {
        $this->buyerName = $buyerName;
        $this->buyerEmail = $buyerEmail;
        $this->invoices = $invoices;
        $this->customMessage = $customMessage;
        $this->companyName = $companyName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Reminder - ' . $this->buyerName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.prompt_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
