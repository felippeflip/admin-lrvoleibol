<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ficha da Comissão Técnica - {{ $comissaoTecnica->nome }}</title>
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
            /* Altura aproximada de meia página A4 (148mm) menos margens */
            height: 135mm; 
            box-sizing: border-box;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10pt;
            color: #6b7280;
        }
        .content {
            display: flex;
            gap: 20px;
        }
        .photo-col {
            width: 25%;
            text-align: center;
        }
        .photo-box {
            width: 110px;
            height: 140px;
            border: 1px solid #d1d5db;
            padding: 2px;
            margin: 0 auto 10px;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .info-col {
            width: 75%;
        }
        .category-badge {
            display: inline-block;
            background-color: #ebf8ff;
            color: #2b6cb0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 600;
            border: 1px solid #bee3f8;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .row {
            display: table-row;
        }
        .col {
            display: table-cell;
            padding-bottom: 5px;
            vertical-align: top;
        }
        .label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            display: block;
        }
        .value {
            font-size: 10pt;
            font-weight: 500;
            color: #111827;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            left: 15px;
            right: 15px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <div class="header">
            <h1>Ficha da Comissão Técnica</h1>
            <p>Liga Regional de Voleibol</p>
        </div>

        <div class="content">
            <!-- Foto e Resumo -->
            <div class="photo-col">
                <div class="photo-box">
                    @if($comissaoTecnica->foto)
                        <img src="{{ $comissaoTecnica->foto_url }}" alt="Foto">
                    @else
                        <div style="width:100%; height:100%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:9px;">SEM FOTO</div>
                    @endif
                </div>
                <div style="font-size: 9pt; font-weight: bold; margin-bottom: 5px;">{{ $comissaoTecnica->nome }}</div>
                <div class="category-badge">{{ $comissaoTecnica->funcao }}</div>
                
                <div style="margin-top: 10px; font-size: 9pt;">
                    <strong>Equipe:</strong><br>{{ $comissaoTecnica->time->tim_nome ?? 'Sem Time Vinculado' }}
                </div>
            </div>

            <!-- Detalhes -->
            <div class="info-col">
                <div class="section-title">Dados Pessoais</div>
                <div class="grid">
                    <div class="row">
                        <div class="col">
                            <span class="label">CPF</span>
                            <span class="value">{{ $comissaoTecnica->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $comissaoTecnica->cpf) : '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">RG</span>
                            <span class="value">{{ $comissaoTecnica->rg ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Registro LRV</span>
                            <span class="value">{{ $comissaoTecnica->registro_lrv ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Registro Profissional (CREF, CRM)</span>
                            <span class="value">{{ $comissaoTecnica->documento_registro ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Data Nasc.</span>
                            <span class="value">{{ $comissaoTecnica->data_nascimento ? \Carbon\Carbon::parse($comissaoTecnica->data_nascimento)->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="section-title">Contato</div>
                <div class="grid">
                    <div class="row">
                        <div class="col">
                            <span class="label">Celular</span>
                            <span class="value">{{ $comissaoTecnica->celular ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $comissaoTecnica->celular) : '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Telefone</span>
                            <span class="value">{{ $comissaoTecnica->telefone ? preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $comissaoTecnica->telefone) : '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" style="grid-column: span 2;">
                            <span class="label">E-mail</span>
                            <span class="value">{{ $comissaoTecnica->email ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="section-title">Endereço</div>
                <div class="grid">
                    <div class="row">
                        <div class="col" style="width: 70%">
                            <span class="label">Logradouro</span>
                            <span class="value">{{ $comissaoTecnica->endereco }}@if($comissaoTecnica->numero), {{ $comissaoTecnica->numero }}@endif</span>
                        </div>
                        <div class="col" style="width: 30%">
                            <span class="label">CEP</span>
                            <span class="value">{{ $comissaoTecnica->cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $comissaoTecnica->cep) : '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Bairro</span>
                            <span class="value">{{ $comissaoTecnica->bairro ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Cidade / UF</span>
                            <span class="value">{{ $comissaoTecnica->cidade ?? '-' }} / {{ $comissaoTecnica->estado ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            Documento gerado em {{ date('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
