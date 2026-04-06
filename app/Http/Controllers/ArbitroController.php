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
        // Busca usuários com role 'Juiz' que estejam ativos
        $query = User::role('Juiz')->where('active', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('apelido', 'like', "%{$search}%")
                    ->orWhere('telefone', 'like', "%{$search}%");
            });
        }

        $arbitros = $query->paginate(10);

        if ($this->isMobileView()) {
            return view('mobile.arbitros.index', compact('arbitros'));
        }

        return view('arbitros.index', compact('arbitros'));
    }
    public function show(Request $request, $id)
    {
        $arbitro = User::findOrFail($id);

        if ($this->isMobileView()) {
            return view('mobile.arbitros.show', compact('arbitro'));
        }

        return view('arbitros.show', compact('arbitro'));
    }
}
