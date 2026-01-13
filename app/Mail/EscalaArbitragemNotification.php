<?php

namespace App\Mail;

use App\Models\Jogo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EscalaArbitragemNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $jogo;
    public $user;
    public $funcao;

    /**
     * Create a new message instance.
     */
    public function __construct(Jogo $jogo, User $user, string $funcao)
    {
        $this->jogo = $jogo;
        $this->user = $user;
        $this->funcao = $funcao;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Escala de Arbitragem - Voleibol - Jogo #' . $this->jogo->jgo_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.escala_arbitragem',
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
