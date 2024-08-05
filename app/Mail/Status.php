<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Status extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;
    /**
     * Create a new message instance.
     */
    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = ucwords(str_replace('_', ' ', $this->reminder->status));
        $smallcasestatus = str_replace('_', ' ', $this->reminder->status);
          $verb = '';
        if($status == 'Pending'){
            $verb = 'is generated ';
            $smallcasestatus = '' ;
        }
        else if($status == 'In Progress'){
            $verb = 'is ';
        }
        else if($status == 'Resolved' || $status == 'Cancelled'){
            $verb = 'has been ';
        }
       

        return new Envelope(
            subject:"Your ticket no. [" . $this->reminder->ticket.'] '. $verb .  $smallcasestatus
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.status',
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
