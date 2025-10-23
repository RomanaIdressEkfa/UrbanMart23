<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Preorder;

class PreorderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The preorder instance.
     *
     * @var \App\Models\Preorder
     */
    public $order;
    public $content_message; // "content" নাম পরিবর্তন করে "content_message" করা হয়েছে

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Preorder $order, string $content_message)
    {
        $this->order = $order;
        $this->content_message = $content_message;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: translate('Your Pre-Order Status Update: ') . ucfirst($this->order->status),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.preorder_status_update',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}