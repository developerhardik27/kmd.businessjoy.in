<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingInvoiceReportMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $companies;
    public $reportData;
    public $dateFrom;
    public $dateTo;
    public $invoiceStatus;
    public $sampleDateFrom;
    public $sampleDateTo;
    
    /**
     * Create a new message instance.
     */
    public function __construct($companies, $reportData, $dateFrom, $dateTo, $invoiceStatus, $sampleDateFrom, $sampleDateTo)
    {
        $this->companies = $companies;
        $this->reportData = $reportData;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->invoiceStatus = $invoiceStatus;
        $this->sampleDateFrom = $sampleDateFrom;
        $this->sampleDateTo = $sampleDateTo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = isset($this->companies[0]) ? $this->companies[0]['name'] : 'Company';
        return new Envelope(
            subject: 'Pending Invoice Report - ' . $companyName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending_invoice_report',
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
