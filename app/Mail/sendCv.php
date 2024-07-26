<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendCv extends Mailable
{
    use Queueable, SerializesModels;


    public $file;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($file,$name)
    {
        $this->file = $file;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cv',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'sendCv',
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


    public function build()
    {
        return $this->view('sendCv')
                    ->attach($this->file, [
                        'as' => $this->name, // اسم الملف الذي سيظهر للمستلم مع امتداد pdf
                        'mime' => 'application/pdf' // نوع الملف PDF
                    ]);
    }


}
