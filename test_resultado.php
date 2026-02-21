<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/jogos/1/resultado', 'POST', [
    'sets' => [
        1 => ['mandante' => 25, 'visitante' => 20],
        2 => ['mandante' => 25, 'visitante' => 20],
        3 => ['mandante' => 25, 'visitante' => 20],
    ]
]);

$controller = app(App\Http\Controllers\ResultadosController::class);
try {
    $response = $controller->store($request, 1);
    var_dump(get_class($response));
    if (method_exists($response, 'getSession')) {
        var_dump($response->getSession()->get('error'));
        var_dump($response->getSession()->get('success'));
        $errors = $response->getSession()->get('errors');
        if($errors) print_r($errors->all());
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
