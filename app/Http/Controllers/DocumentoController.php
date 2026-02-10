<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



    use App\Models\Documento;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    
    class DocumentoController extends Controller
    {
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Todos podem ver documentos ativos. Admins veem tudo.
        if (auth()->user()->hasRole('Administrador')) {
            $documentos = Documento::orderBy('created_at', 'desc')->paginate(10);
        } else {
            $documentos = Documento::where('ativo', true)->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('documentos.index', compact('documentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        return view('documentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'arquivo' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240', // 10MB
        ]);

        $file = $request->file('arquivo');
        $extension = $file->getClientOriginalExtension();
        $tipo = in_array(strtolower($extension), ['pdf']) ? 'pdf' : 'imagem';
        $filename = Str::slug($request->titulo) . '_' . time() . '.' . $extension;

        try {
            $path = Storage::disk('documentos_uploads')->putFileAs('/', $file, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao fazer upload do arquivo.');
        }

        Documento::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'caminho_arquivo' => $filename,
            'tipo' => $tipo,
            'ativo' => true,
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        $documento = Documento::findOrFail($id);
        return view('documentos.edit', compact('documento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        
        $documento = Documento::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'arquivo' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'ativo' => 'boolean',
        ]);

        if ($request->hasFile('arquivo')) {
            // Delete old file
            if (Storage::disk('documentos_uploads')->exists($documento->caminho_arquivo)) {
                Storage::disk('documentos_uploads')->delete($documento->caminho_arquivo);
            }

            $file = $request->file('arquivo');
             $extension = $file->getClientOriginalExtension();
            $tipo = in_array(strtolower($extension), ['pdf']) ? 'pdf' : 'imagem';
            $filename = Str::slug($request->titulo) . '_' . time() . '.' . $extension;

            try {
                 Storage::disk('documentos_uploads')->putFileAs('/', $file, $filename);
                 $documento->caminho_arquivo = $filename;
                 $documento->tipo = $tipo;
            } catch (\Exception $e) {
                return back()->with('error', 'Erro ao fazer upload do arquivo.');
            }
        }

        $documento->titulo = $request->titulo;
        $documento->descricao = $request->descricao;
        $documento->ativo = $request->has('ativo'); // Checkbox handling
        $documento->save();

        return redirect()->route('documentos.index')->with('success', 'Documento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        $documento = Documento::findOrFail($id);
        
        if (Storage::disk('documentos_uploads')->exists($documento->caminho_arquivo)) {
            Storage::disk('documentos_uploads')->delete($documento->caminho_arquivo);
        }

        $documento->delete();
        return redirect()->route('documentos.index')->with('success', 'Documento removido com sucesso!');
    }
}
