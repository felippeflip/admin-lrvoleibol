<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Jogo;
use Carbon\Carbon;

$total = Jogo::count();
echo "Total Games: " . $total . "\n";

$startDate = now()->subDays(7)->startOfDay();
$endDate = now()->addDays(7)->endOfDay();

$inRange = Jogo::whereBetween('jgo_dt_jogo', [$startDate, $endDate])->count();
echo "Games in Range (-7 to +7 days): " . $inRange . "\n";

echo "Range: " . $startDate->toDateString() . " to " . $endDate->toDateString() . "\n";

$allGames = Jogo::orderBy('jgo_dt_jogo')->get();
foreach ($allGames as $g) {
    echo "ID: {$g->jgo_id} - Date: {$g->jgo_dt_jogo}\n";
}
