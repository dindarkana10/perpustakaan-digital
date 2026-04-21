<?php

namespace App\Mail;

use App\Models\Pengembalian;
use App\Models\Denda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
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
        return new Envelope(
            subject: 'Struk Pengembalian Alat - ' . $this->pengembalian->peminjaman->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.struk-pengembalian',
        );
    }

    public function attachments(): array
    {
        $pengembalian = $this->pengembalian;
        $denda        = Denda::first();

        // ✅ Resolve detailSource sama seperti di controller
        $detailSource = $pengembalian->details->isNotEmpty()
            ? $pengembalian->details
            : $pengembalian->peminjaman->details->map(function ($d) {
                $d->jumlah_kembali     = $d->jumlah;
                $d->kondisi_kembali    = 'baik';
                $d->keterangan_kondisi = '-';
                return $d;
            });

        $pdf = Pdf::loadView('pdf.struk-pengembalian', [
            'pengembalian' => $pengembalian,
            'denda'        => $denda,
            'detailSource' => $detailSource,
        ])->setPaper([0, 0, 226.77, 595.28]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'struk-pengembalian-' . str_pad($pengembalian->id, 6, '0', STR_PAD_LEFT) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}