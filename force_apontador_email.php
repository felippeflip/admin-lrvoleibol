<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Jogo;

$jogo = Jogo::find(2);
if ($jogo) {
    echo "Reseting Apontador Flag for Game 2...\n";
    $jogo->jgo_notificacao_apontador = false;
    $jogo->saveQuietly(); // Salva sem disparar eventos ainda

    echo "Disparando update no jogo para acionar Observer...\n";
    // Forcar um update 'fake' que nao muda dados reais mas dispara o updated event?
    // O Observer checa: wasChanged('jgo_apontador') || !$jogo->jgo_notificacao_apontador
    // Como resetamos a flag para FALSE acima, a segunda condição (!$jogo->jgo_notificacao_apontador) será TRUE.
    // Portanto, QUALQUER update no modelo deve disparar o envio.
    
    $jogo->save(); // Dispara updated

    echo "Update realizado. Verifique o Mailtrap e o Log.\n";
}
