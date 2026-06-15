<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingSampleReportMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $companies;
    public $reportData;
    public $dateFrom;
    public $dateTo;
    public $sampleStatus;
    
    /**
     * Create a new message instance.
     */
    public function __construct($companies, $reportData, $dateFrom, $dateTo, $sampleStatus)
    {
        $this->companies = $companies;
        $this->reportData = $reportData;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->sampleStatus = $sampleStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = isset($this->companies[0]) ? $this->companies[0]['name'] : 'Company';
        return new Envelope(
            subject: 'Pending Sample Report - ' . $companyName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending_sample_report',
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
