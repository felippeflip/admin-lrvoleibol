<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jogo = App\Models\Jogo::first();
if ($jogo) {
    echo "ID: " . $jogo->jgo_id . "\n";
    echo "Date: " . $jogo->jgo_dt_jogo . "\n";
    echo "Status: " . $jogo->jgo_status . "\n";
    echo "Res Status: [" . $jogo->jgo_res_status . "]\n"; // Brackets to see if empty or space
    echo "Is Null? " . (is_null($jogo->jgo_res_status) ? 'YES' : 'NO') . "\n";
} else {
    echo "No game found.\n";
}
