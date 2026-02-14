<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Jogo;
use App\Models\User;

// Pegar o ultimo jogo com apontador
$jogo = Jogo::whereNotNull('jgo_apontador')->orderBy('jgo_id', 'desc')->first();

if ($jogo) {
    echo "Jogo ID: " . $jogo->jgo_id . "\n";
    echo "Apontador ID: " . $jogo->jgo_apontador . "\n";
    
    $apontador = $jogo->apontador;
    if ($apontador) {
        echo "Apontador Nome: " . $apontador->name . "\n";
        echo "Apontador Email: " . $apontador->email . "\n";
    } else {
        echo "Objeto Apontador nao carregado/null.\n";
    }

    echo "Flag Notificacao Apontador: " . ($jogo->jgo_notificacao_apontador ? 'DEVERIA TER SIDO ENVIADO' : 'NAO ENVIADO') . "\n";
} else {
    echo "Nenhum jogo com apontador encontrado.\n";
}
