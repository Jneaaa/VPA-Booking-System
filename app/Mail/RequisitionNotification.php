<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisitionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $form;
    public $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct($form, $messageText)
    {
        $this->form = $form;
        $this->messageText = $messageText;
    }

        public function build()
    {
        return $this->subject('Requisition Update: ' . $this->form->purpose)
                    ->markdown('emails.requisition.notification');
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Requisition Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.requisition.notification',
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
