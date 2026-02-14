<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #1e40af; color: white; padding: 10px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .details { background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { display: inline-block; background-color: #16a34a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; text-align: center;}
        .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/LOGO_LRV-150x150.png')) }}" alt="Logo LRV" style="display: block; margin: 0 auto 10px auto; max-width: 150px;">
            <h2>Escala de Arbitragem</h2>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $user->name }}</strong>.</p>
            <p>Você foi escalado(a) como <strong>{{ $funcao }}</strong> para a seguinte partida:</p>
            
            <div class="details">
                <p><strong>Campeonato:</strong> {{ $jogo->mandante->campeonato->cpo_nome ?? 'N/A' }}</p>
                <p><strong>Categoria:</strong> {{ $jogo->mandante->equipe->categoria->cto_nome ?? 'N/A' }}</p>
                <p><strong>Partida:</strong> {{ $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante' }} x {{ $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante' }}</p>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }}</p>
                <p><strong>Horário:</strong> {{ $jogo->jgo_hora_jogo }}</p>
                <p><strong>Local:</strong> {{ $jogo->ginasio->gin_nome ?? 'Local a definir' }}</p>
                @if($jogo->ginasio)
                    <p><strong>Endereço:</strong> {{ $jogo->ginasio->gin_endereco }}, {{ $jogo->ginasio->gin_numero }} - {{ $jogo->ginasio->gin_bairro }}, {{ $jogo->ginasio->gin_cidade }}</p>
                @endif
                <hr style="border: 0; border-top: 1px solid #ccc; margin: 10px 0;">
                <p><strong>Árbitro Principal:</strong> {{ $jogo->arbitroPrincipal->name ?? 'Não definido' }}</p>
                <p><strong>Árbitro Secundário:</strong> {{ $jogo->arbitroSecundario->name ?? 'Não definido' }}</p>
                <p><strong>Apontador:</strong> {{ $jogo->apontador->name ?? 'Não definido' }}</p>
            </div>

            @if($jogo->ginasio)
                <div style="text-align: center;">
                    <a href="{{ $jogo->ginasio->google_maps_link }}" class="btn">Abrir Local no Google Maps</a>
                </div>
            @endif

            <p>Por favor, chegue com antecedência ao local.</p>
        </div>
        <div class="footer">
            <p style="margin-bottom: 0;">Este é um e-mail automático do Sistema de Gestão de Voleibol</p>
            <p style="margin-top: 5px;">LIGA REGIONAL DE VOLEIBOL - CAMPINAS</p>
            <p>Favor não responder a este e-mail.</p>
        </div>
    </div>
</body>
</html>
