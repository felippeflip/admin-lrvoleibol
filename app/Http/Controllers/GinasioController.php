<?php

namespace App\Http\Controllers;

use App\Models\Ginasio;
use App\Models\Time;
use Illuminate\Http\Request;

class GinasioController extends Controller
{
    public function index(Request $request)
    {
        $query = Ginasio::with('time');

        if ($request->filled('nome')) {
            $query->where('gin_nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('cidade')) {
            $query->where('gin_cidade', 'like', '%' . $request->cidade . '%');
        }

        if ($request->filled('time_id')) {
            $query->where('gin_tim_id', $request->time_id);
        }

        $status = $request->input('status', 'active');
        if ($status !== 'todos') {
            $isActive = $status === 'active' ? 1 : 0;
            $query->where('gin_status', $isActive);
        }

        $ginasios = $query->paginate(10)->appends($request->all());
        $times = Time::orderBy('tim_nome')->get();

        return view('ginasios.index', compact('ginasios', 'times'));
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

    public function toggleStatus(Ginasio $ginasio)
    {
        $ginasio->gin_status = !$ginasio->gin_status;
        $ginasio->save();

        $statusName = $ginasio->gin_status ? 'ativado' : 'desativado';
        return redirect()->route('ginasios.index')->with('success', "Ginásio {$statusName} com sucesso.");
    }
}
