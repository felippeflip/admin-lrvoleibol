<?php

namespace App\Http\Controllers;

use App\Models\Ginasio;
use App\Models\Time;
use Illuminate\Http\Request;

class GinasioController extends Controller
{
    public function index()
    {
        $ginasios = Ginasio::with('time')->paginate(10);
        return view('ginasios.index', compact('ginasios'));
    }

    public function create()
    {
        $times = Time::all();
        return view('ginasios.create', compact('times'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gin_nome' => 'required|string|max:255',
            'gin_cep' => 'required|string|max:10',
            'gin_endereco' => 'required|string|max:255',
            'gin_numero' => 'required|string|max:20',
            'gin_bairro' => 'required|string|max:255',
            'gin_cidade' => 'required|string|max:255',
            'gin_estado' => 'required|string|max:2',
            'gin_tim_id' => 'nullable|exists:times,tim_id',
        ]);

        Ginasio::create($request->all());

        return redirect()->route('ginasios.index')->with('success', 'Ginásio criado com sucesso.');
    }

    public function edit(Ginasio $ginasio)
    {
        $times = Time::all();
        return view('ginasios.edit', compact('ginasio', 'times'));
    }

    public function update(Request $request, Ginasio $ginasio)
    {
        $request->validate([
            'gin_nome' => 'required|string|max:255',
            'gin_cep' => 'required|string|max:10',
            'gin_endereco' => 'required|string|max:255',
            'gin_numero' => 'required|string|max:20',
            'gin_bairro' => 'required|string|max:255',
            'gin_cidade' => 'required|string|max:255',
            'gin_estado' => 'required|string|max:2',
            'gin_tim_id' => 'nullable|exists:times,tim_id',
        ]);

        $ginasio->update($request->all());

        return redirect()->route('ginasios.index')->with('success', 'Ginásio atualizado com sucesso.');
    }

    public function destroy(Ginasio $ginasio)
    {
        $ginasio->delete();
        return redirect()->route('ginasios.index')->with('success', 'Ginásio excluído com sucesso.');
    }
}
