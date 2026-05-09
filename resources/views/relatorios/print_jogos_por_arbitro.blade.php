<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatório de Atuação - {{ $arbitroSelecionado->name }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0.5cm;
            }
            body {
                margin: 0;
                padding: 0;
                font-family: 'Figtree', sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: 'Figtree', sans-serif;
            background: white;
            color: #1f2937;
        }
        .container {
            width: 100%;
            max-width: 190mm; /* Largura útil A4 */
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            width: 120px;
            text-align: left;
        }
        .header-left img {
            max-height: 80px;
            max-width: 100%;
            display: block;
        }
        .header-center {
            flex: 1;
            text-align: center;
        }
        .header-center h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .header-center p {
            margin: 5px 0 0;
            font-size: 10pt;
            color: #6b7280;
        }
        .header-right {
            width: 120px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-block {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }
        th, td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #d1d5db;
        }
        .role-badge {
            background-color: #ebf8ff;
            color: #2b6cb0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
            border: 1px solid #bee3f8;
            display: inline-block;
            margin-bottom: 2px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-family: 'Figtree', sans-serif;">Imprimir / Salvar PDF</button>
    </div>

    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="{{ asset('images/LOGO_LRV-150x150.png') }}" alt="LRV Logo">
            </div>
            <div class="header-center">
                <h1>Relatório de Atuação - Árbitro/Apontador</h1>
                <p>Liga Regional de Voleibol</p>
            </div>
            <div class="header-right"></div>
        </div>

    <div class="info-block">
        <div>
            <strong>Nome:</strong> {{ $arbitroSelecionado->name }}<br>
            <strong>Ano de Referência:</strong> {{ $ano }}
        </div>
        <div>
            <strong>Total de Jogos Atuados:</strong> {{ count($jogos) }}<br>
            <strong>Data de Emissão:</strong> {{ date('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data / Hora</th>
                <th>Campeonato</th>
                <th>Categoria / Fase</th>
                <th>Confronto</th>
                <th>Local</th>
                <th>Função</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jogos as $index => $jogo)
                @php
                    $funcao = [];
                    if ($jogo->jgo_arbitro_principal == $arbitroSelecionado->id) $funcao[] = 'Árbitro Principal';
                    if ($jogo->jgo_arbitro_secundario == $arbitroSelecionado->id) $funcao[] = 'Árbitro Secundário';
                    if ($jogo->jgo_apontador == $arbitroSelecionado->id) $funcao[] = 'Apontador';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $jogo->jgo_dt_jogo ? \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') : 'N/A' }}<br>
                        {{ $jogo->jgo_hora_jogo ? \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') : 'N/A' }}
                    </td>
                    <td>{{ $jogo->mandante->campeonato->cpo_nome ?? 'N/A' }}</td>
                    <td>
                        {{ $jogo->mandante->equipe->categoria->cto_nome ?? 'N/A' }}<br>
                        {{ $jogo->jgo_fase }}
                    </td>
                    <td>
                        <strong>{{ $jogo->mandante->equipe->eqp_nome_detalhado ?? 'N/A' }}</strong>
                        <br>vs<br>
                        <strong>{{ $jogo->visitante->equipe->eqp_nome_detalhado ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $jogo->ginasio->gin_nome ?? 'N/A' }}</td>
                    <td>
                        @foreach($funcao as $f)
                            <div class="role-badge">{{ $f }}</div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Nenhum jogo encontrado no período selecionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

        <div class="footer">
            Documento gerado automaticamente pelo Sistema LRVoleibol em {{ date('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
