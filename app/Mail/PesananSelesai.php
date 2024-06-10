<?php

namespace App\Mail;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class PesananSelesai extends Mailable
{
    use Queueable, SerializesModels;

    public $id_pesanan;
    /**
     * Create a new message instance.
     */
    public function __construct(string $id_pesanan)
    {
        $this->id_pesanan = $id_pesanan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan Selesai',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $pesanan = Pesanan::where('id_pesanan', $this->id_pesanan)->with('detail')->first();
        return new Content(
            view: 'Email.pesanan.emailselesai',
            with: [
                'pesanan' => $pesanan,
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
