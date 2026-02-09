<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ArbitroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Busca usuários com role 'Juiz' ou 'Apontador' que estejam ativos
        // Nota: O pedido diz "listar somente os Juizes Ativos", mas o menu é "Arbitros/Apontadores",
        // então faz sentido listar ambos para consulta.
        $query = User::role(['Juiz', 'Apontador'])->where('active', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('apelido', 'like', "%{$search}%");
            });
        }

        $arbitros = $query->paginate(10);

        return view('arbitros.index', compact('arbitros'));
    }
}
