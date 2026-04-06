<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PeminjamanMail extends Mailable
{
    use Queueable, SerializesModels;

    public Peminjaman $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->peminjaman->kode_peminjaman . '] Struk Peminjaman Alat',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.peminjaman',
        );
    }
}
