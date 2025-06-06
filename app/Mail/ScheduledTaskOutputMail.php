<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduledTaskOutputMail extends Mailable
{
    use Queueable, SerializesModels;

    public $outputFile;

    /**
     * Create a new message instance.
     */
    public function __construct($outputFile)
    {
        $this->outputFile = $outputFile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Scheduled Task Output Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        // Get the contents of the output file to send it inside the email body
        $outputContent = File::get($this->outputFile);

        return new Content(
            view: 'emails.scheduled_task_output',  // A blade view to format the content if needed
            with: [
                'outputContent' => $outputContent  // Pass the file content to the view
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Attach the output file correctly using the `attach()` method
        return [];
    }
}
