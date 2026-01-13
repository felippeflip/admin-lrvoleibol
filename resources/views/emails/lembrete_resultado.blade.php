<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #f59e0b; color: white; padding: 10px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .details { background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; text-align: center;}
        .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Lembrete de Apontamento</h2>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $user->name }}</strong>.</p>
            <p>A partida abaixo encerrou há mais de 3 horas e o resultado ainda não foi apontado no sistema.</p>
            
            <div class="details">
                <p><strong>Partida:</strong> {{ $jogo->mandante->equipe->eqp_nome ?? 'Mandante' }} x {{ $jogo->visitante->equipe->eqp_nome ?? 'Visitante' }}</p>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }}</p>
                <p><strong>Horário:</strong> {{ $jogo->jgo_hora_jogo }}</p>
                <p><strong>Local:</strong> {{ $jogo->ginasio->gin_nome ?? 'Local a definir' }}</p>
            </div>

            <p>Por favor, clique no botão abaixo para acessar a tela de apontamento e inserir os sets:</p>

            <div style="text-align: center;">
                {{-- Using local ID for route --}}
                <a href="{{ route('resultados.create', $jogo->jgo_id) }}" class="btn">Inserir Resultado</a>
            </div>

            <p>Caso tenha dificuldades, entre em contato com a administração.</p>
        </div>
        <div class="footer">
            <p>Este é um e-mail automático do Sistema de Gestão de Voleibol.</p>
        </div>
    </div>
</body>
</html>
