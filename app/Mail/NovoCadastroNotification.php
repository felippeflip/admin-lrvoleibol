<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NovoCadastroNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $registro; // Atleta or ComissaoTecnica instance
    public $tipo; // "Atleta" or "Comissão Técnica"
    public $criador; // User who created it
    public $time; // String name of the team

    /**
     * Create a new message instance.
     */
    public function __construct($registro, string $tipo, User $criador, string $time)
    {
        $this->registro = $registro;
        $this->tipo = $tipo;
        $this->criador = $criador;
        $this->time = $time;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $nome = ($this->tipo == 'Atleta') ? $this->registro->atl_nome : $this->registro->nome;
        return new Envelope(
            subject: 'Novo Cadastro de ' . $this->tipo . ': ' . $nome,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.novo_cadastro',
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
