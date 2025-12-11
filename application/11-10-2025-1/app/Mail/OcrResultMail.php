<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OcrResultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public bool $success;
    public string $message;
    public int $volumeId;

    /**
     * Create a new message instance.
     *
     * @param bool $success
     * @param string $message
     * @param int $volumeId
     */
    public function __construct(bool $success, string $message, int $volumeId)
    {
        $this->success = $success;
        $this->message = $message;
        $this->volumeId = $volumeId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->success
            ? 'OCR Processing Succeeded'
            : 'OCR Processing Failed';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ocr_result',
            with: [
                'success' => $this->success,
                'message' => $this->message,
                'volumeId' => $this->volumeId,
            ],
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
