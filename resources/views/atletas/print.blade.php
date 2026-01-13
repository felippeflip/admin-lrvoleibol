<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ficha do Atleta - {{ $atleta->atl_nome }}</title>
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
            <h1>Ficha do Atleta</h1>
            <p>Liga Regional de Voleibol</p>
        </div>

        <div class="content">
            <!-- Foto e Resumo -->
            <div class="photo-col">
                <div class="photo-box">
                    @if($atleta->atl_foto)
                        <img src="{{ $atleta->atl_foto_url }}" alt="Foto">
                    @else
                        <div style="width:100%; height:100%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:9px;">SEM FOTO</div>
                    @endif
                </div>
                <div style="font-size: 9pt; font-weight: bold; margin-bottom: 5px;">{{ $atleta->atl_nome }}</div>
                <div class="category-badge">{{ $atleta->categoria->cto_nome ?? 'Sem Categoria' }}</div>
                
                <div style="margin-top: 10px; font-size: 9pt;">
                    <strong>Idade:</strong> {{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age . ' anos' : '-' }}<br>
                    <strong>Sexo:</strong> {{ $atleta->atl_sexo == 'M' ? 'Masculino' : ($atleta->atl_sexo == 'F' ? 'Feminino' : 'Outro') }}
                </div>
            </div>

            <!-- Detalhes -->
            <div class="info-col">
                <div class="section-title">Dados Pessoais</div>
                <div class="grid">
                    <div class="row">
                        <div class="col">
                            <span class="label">CPF</span>
                            <span class="value">{{ $atleta->atl_cpf_formatted ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">RG</span>
                            <span class="value">{{ $atleta->atl_rg_formatted ?? '-' }} {{ $atleta->atl_resg ? '('.$atleta->atl_resg.')' : '' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Data Nasc.</span>
                            <span class="value">{{ $atleta->atl_dt_nasc_formatted ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Ano Inscrição</span>
                            <span class="value">{{ $atleta->atl_ano_insc ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="section-title">Contato</div>
                <div class="grid">
                    <div class="row">
                        <div class="col">
                            <span class="label">Celular</span>
                            <span class="value">{{ $atleta->atl_celular_formatted ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Telefone</span>
                            <span class="value">{{ $atleta->atl_telefone_formatted ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" style="grid-column: span 2;">
                            <span class="label">E-mail</span>
                            <span class="value">{{ $atleta->atl_email ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="section-title">Endereço</div>
                <div class="grid">
                    <div class="row">
                        <div class="col" style="width: 70%">
                            <span class="label">Logradouro</span>
                            <span class="value">{{ $atleta->atl_endereco }}@if($atleta->atl_numero), {{ $atleta->atl_numero }}@endif</span>
                        </div>
                        <div class="col" style="width: 30%">
                            <span class="label">CEP</span>
                            <span class="value">{{ $atleta->atl_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $atleta->atl_cep) : '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Bairro</span>
                            <span class="value">{{ $atleta->atl_bairro ?? '-' }}</span>
                        </div>
                        <div class="col">
                            <span class="label">Cidade / UF</span>
                            <span class="value">{{ $atleta->atl_cidade ?? '-' }} / {{ $atleta->atl_estado ?? '-' }}</span>
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
