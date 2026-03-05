<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class TorneioInicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso negado.');
        }

        $categorias = Categoria::orderBy('cto_nome', 'asc')->get();
        $arquivosImportados = [];

        foreach ($categorias as $categoria) {
            $filename = $categoria->cto_slug . '.html';
            if (Storage::disk('public')->exists('torneio_inicio/' . $filename)) {
                $categoria->file_url = asset('storage/torneio_inicio/' . $filename);
                $categoria->file_name = $filename;
                $arquivosImportados[] = $categoria;
            }
        }

        return view('torneio_inicio.index', compact('arquivosImportados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso negado.');
        }

        $categorias = Categoria::orderBy('cto_nome', 'asc')->get();
        return view('torneio_inicio.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'categoria_id' => 'required|exists:categorias,cto_id',
            'arquivo'      => 'required|file|mimes:html|max:10240', // HTML file size up to 10MB
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);
        
        $file = $request->file('arquivo');
        $filename = $categoria->cto_slug . '.html';

        try {
            Storage::disk('public')->putFileAs('torneio_inicio', $file, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao fazer upload do arquivo.');
        }

        return redirect()->route('torneio-inicio.index')->with('success', 'Arquivo importado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso negado.');
        }

        $categoria = Categoria::findOrFail($id);
        // It acts effectively the same as create but pre-selects the category
        $categorias = Categoria::orderBy('cto_nome', 'asc')->get();

        return view('torneio_inicio.edit', compact('categoria', 'categorias'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso negado.');
        }
        
        $categoria = Categoria::findOrFail($id);
        $filename = $categoria->cto_slug . '.html';

        if (Storage::disk('public')->exists('torneio_inicio/' . $filename)) {
            Storage::disk('public')->delete('torneio_inicio/' . $filename);
            return redirect()->route('torneio-inicio.index')->with('success', 'Arquivo removido com sucesso!');
        }

        return redirect()->route('torneio-inicio.index')->with('error', 'Arquivo não encontrado.');
    }
}
