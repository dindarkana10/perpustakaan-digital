<?php

namespace App\Mail;

use App\Models\Pengembalian;
use App\Models\Denda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class StrukPengembalianMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pengembalian $pengembalian;

    public function __construct(Pengembalian $pengembalian)
    {
        $this->pengembalian = $pengembalian;
    }

    public function envelope(): Envelope
    {
        $nama  = $this->pengembalian->peminjaman->user->name ?? 'Peminjam';
        $noTrx = str_pad($this->pengembalian->id, 6, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "[Perpustakaan Digital] Struk Pengembalian Buku #{$noTrx} - {$nama}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.struk-pengembalian',
            with: ['pengembalian' => $this->pengembalian],
        );
    }

    public function attachments(): array
    {
        $pengembalian = $this->pengembalian;

        // Resolve detail source
        $detailSource = $pengembalian->details->isNotEmpty()
            ? $pengembalian->details
            : $pengembalian->peminjaman->details->map(function ($d) {
                $d->jumlah_kembali     = $d->jumlah ?? 1;
                $d->kondisi_kembali    = 'baik';
                $d->keterangan_kondisi = '-';
                return $d;
            });

        $pdf = Pdf::loadView('pdf.struk-pengembalian', [
            'pengembalian' => $pengembalian,
            'detailSource' => $detailSource,
        ])->setPaper('A5', 'portrait');

        $noTrx = str_pad($pengembalian->id, 6, '0', STR_PAD_LEFT);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "struk-pengembalian-{$noTrx}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}