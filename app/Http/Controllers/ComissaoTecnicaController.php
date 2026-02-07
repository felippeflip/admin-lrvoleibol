<?php

namespace App\Http\Controllers;

use App\Models\ComissaoTecnica;
use App\Models\Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ComissaoTecnicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ComissaoTecnica::with('time');

        // Escopo baseado em Função (Role)
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if ($time) {
                $query->where('time_id', $time->tim_id);
            } else {
                // Se não tem time vinculado, retorna lista vazia
                $comissao = ComissaoTecnica::where('id', 0)->paginate(10);
                $times = [];
                $funcoes = ['Técnico', 'Assistente Técnico', 'Médico', 'Fisioterapeuta', 'Massagista'];
                return view('comissao_tecnica.index', compact('comissao', 'times', 'funcoes'));
            }
        }

        // Filtros
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        if ($request->filled('time_id') && auth()->user()->hasRole('Administrador')) {
            $query->where('time_id', $request->time_id);
        }
        if ($request->filled('cpf')) {
             $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $query->where('cpf', 'like', '%' . $cpf . '%');
        }
        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }
         if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $comissao = $query->paginate(10)->appends($request->all());
        
        $times = [];
        if (auth()->user()->hasRole('Administrador')) {
             $times = Time::orderBy('tim_nome')->get();
        }
        $funcoes = ['Técnico', 'Assistente Técnico', 'Médico', 'Fisioterapeuta', 'Massagista'];

        return view('comissao_tecnica.index', compact('comissao', 'times', 'funcoes'));
    }

    // ... (create, store, edit, update methods remain mostly the same, ensuring status default is handled in migration)

    public function toggleStatus($id)
    {
        $comissaoTecnica = ComissaoTecnica::findOrFail($id);
        $user = auth()->user();

         if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
             $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $comissaoTecnica->time_id != $time->tim_id) {
                abort(403);
            }
        }

        $comissaoTecnica->status = !$comissaoTecnica->status;
        $comissaoTecnica->save();

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $times = [];
        if (auth()->user()->hasRole('Administrador')) {
            $times = Time::orderBy('tim_nome')->get();
        }
        
        $funcoes = ['Técnico', 'Assistente Técnico', 'Médico', 'Fisioterapeuta', 'Massagista'];

        return view('comissao_tecnica.create', compact('times', 'funcoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time) {
                return redirect()->back()->withErrors(['error' => 'Você não possui um time vinculado.']);
            }
            $request->merge(['time_id' => $time->tim_id]);
        }

        // Limpeza de campos
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf ?? ''),
            'rg' => preg_replace('/[^0-9]/', '', $request->rg ?? ''),
            'celular' => preg_replace('/[^0-9]/', '', $request->celular ?? ''),
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone ?? ''),
            'cep' => preg_replace('/[^0-9]/', '', $request->cep ?? ''),
        ]);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:11|unique:comissao_tecnicas,cpf',
            'rg' => 'nullable|string|max:20',
            'funcao' => 'required|string|in:Técnico,Assistente Técnico,Médico,Fisioterapeuta,Massagista',
            'documento_registro' => 'required|string|max:50', // CREF, CRM, etc.
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Foto 3x4
            'comprovante_documento' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // Diploma/Carteira
            'celular' => 'nullable|string|max:15',
            'telefone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:8',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'time_id' => 'required|exists:times,tim_id',
        ]);

        $data = $request->except(['foto', 'comprovante_documento']);
        $data['status'] = true; // Default active

        // Upload Foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('comissao_fotos')->putFileAs('/', $file, $filename);
            $data['foto'] = $filename;
        }

        // Upload Comprovante
        if ($request->hasFile('comprovante_documento')) {
            $file = $request->file('comprovante_documento');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('comissao_docs')->putFileAs('/', $file, $filename);
            $data['comprovante_documento'] = $filename;
        }

        ComissaoTecnica::create($data);

        return redirect()->route('comissao-tecnica.index')->with('success', 'Membro da comissão técnica cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $comissaoTecnica = ComissaoTecnica::findOrFail($id);
        $user = auth()->user();

        // Check permission
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $comissaoTecnica->time_id != $time->tim_id) {
                abort(403);
            }
        }

        $times = [];
        if ($user->hasRole('Administrador')) {
            $times = Time::orderBy('tim_nome')->get();
        }
        
        $funcoes = ['Técnico', 'Assistente Técnico', 'Médico', 'Fisioterapeuta', 'Massagista'];

        return view('comissao_tecnica.edit', compact('comissaoTecnica', 'times', 'funcoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comissaoTecnica = ComissaoTecnica::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
             $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $comissaoTecnica->time_id != $time->tim_id) {
                abort(403);
            }
             $request->merge(['time_id' => $time->tim_id]);
        }

        // Limpeza
         $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf ?? ''),
            'rg' => preg_replace('/[^0-9]/', '', $request->rg ?? ''),
            'celular' => preg_replace('/[^0-9]/', '', $request->celular ?? ''),
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone ?? ''),
            'cep' => preg_replace('/[^0-9]/', '', $request->cep ?? ''),
        ]);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:11|unique:comissao_tecnicas,cpf,' . $id,
            'rg' => 'nullable|string|max:20',
            'funcao' => 'required|string|in:Técnico,Assistente Técnico,Médico,Fisioterapeuta,Massagista',
            'documento_registro' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'comprovante_documento' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'celular' => 'nullable|string|max:15',
            'telefone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:8',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'time_id' => 'required|exists:times,tim_id',
        ]);

        $data = $request->except(['foto', 'comprovante_documento']);

        // Upload Foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
             // Delete old
            if ($comissaoTecnica->foto && Storage::disk('comissao_fotos')->exists($comissaoTecnica->foto)) {
                Storage::disk('comissao_fotos')->delete($comissaoTecnica->foto);
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('comissao_fotos')->putFileAs('/', $file, $filename);
            $data['foto'] = $filename;
        }

        // Upload Comprovante
        if ($request->hasFile('comprovante_documento')) {
            $file = $request->file('comprovante_documento');
             // Delete old
            if ($comissaoTecnica->comprovante_documento && Storage::disk('comissao_docs')->exists($comissaoTecnica->comprovante_documento)) {
                Storage::disk('comissao_docs')->delete($comissaoTecnica->comprovante_documento);
            }
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('comissao_docs')->putFileAs('/', $file, $filename);
            $data['comprovante_documento'] = $filename;
        }

        $comissaoTecnica->update($data);

        return redirect()->route('comissao-tecnica.index')->with('success', 'Membro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comissaoTecnica = ComissaoTecnica::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
             $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $comissaoTecnica->time_id != $time->tim_id) {
                abort(403);
            }
        }

        // Delete files
        if ($comissaoTecnica->foto && Storage::disk('comissao_fotos')->exists($comissaoTecnica->foto)) {
            Storage::disk('comissao_fotos')->delete($comissaoTecnica->foto);
        }
        if ($comissaoTecnica->comprovante_documento && Storage::disk('comissao_docs')->exists($comissaoTecnica->comprovante_documento)) {
            Storage::disk('comissao_docs')->delete($comissaoTecnica->comprovante_documento);
        }

        $comissaoTecnica->delete();

        return redirect()->route('comissao-tecnica.index')->with('success', 'Membro removido com sucesso!');
    }
}
