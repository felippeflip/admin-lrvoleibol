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
            <h2>Novo Cadastro Realizado</h2>
        </div>
        <div class="content">
            <p>Olá Administrador,</p>
            <p>Um novo cadastro de <strong>{{ $tipo }}</strong> foi realizado no sistema.</p>
            
            <div class="details">
                <p><strong>Time:</strong> {{ $time }}</p>
                @if($tipo == 'Atleta')
                    <p><strong>Nome do Atleta:</strong> {{ $registro->atl_nome }}</p>
                @else
                    <p><strong>Nome da Comissão Técnica:</strong> {{ $registro->nome }}</p>
                @endif
                <p><strong>Data/Hora Criação:</strong> {{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') }}</p>
                <p><strong>Cadastrado por:</strong> {{ $criador->name }}</p>
            </div>

            <div style="text-align: center;">
                 @if($tipo == 'Atleta')
                    <a href="{{ route('atletas.show', $registro->atl_id) }}" class="btn">Visualizar Atleta</a>
                 @else
                    <a href="{{ route('comissao-tecnica.show', $registro->id) }}" class="btn">Visualizar Membro</a>
                 @endif
            </div>

        </div>
        <div class="footer">
            <p style="margin-bottom: 0;">Este é um e-mail automático do Sistema de Gestão de Voleibol</p>
            <p style="margin-top: 5px;">LIGA REGIONAL DE VOLEIBOL - CAMPINAS</p>
            <p>Favor não responder a este e-mail.</p>
        </div>
    </div>
</body>
</html>
