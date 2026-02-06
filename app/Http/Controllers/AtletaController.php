<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Time;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para manipulação de arquivos
use Illuminate\Support\Str; // Para gerar nomes de arquivo únicos
use Illuminate\Support\Facades\Log; // Para logs de erro

class AtletaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Atleta::with(['categoria', 'time']);

        // Escopo baseado em Função (Role)
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if ($time) {
                $query->where('atl_tim_id', $time->tim_id);
            } else {
                // Se não tem time vinculado, retorna lista vazia
                $atletas = Atleta::where('atl_id', 0)->paginate(10);
                return view('atletas.index', compact('atletas'));
            }
        }

        // Filtros
        // 1. Ativo / Inativo (Padrão: Ativo)
        $ativo = $request->input('ativo', '1');
        if ($ativo !== 'todos') {
            $query->where('atl_ativo', $ativo);
        }

        // Filtro por Nome
        if ($request->filled('nome')) {
            $query->where('atl_nome', 'like', '%' . $request->nome . '%');
        }

        // Filtro por CPF
        if ($request->filled('cpf')) {
            // Remove máscara para busca
            $cpfBusca = preg_replace('/[^0-9]/', '', $request->cpf);
            $query->where('atl_cpf', 'like', '%' . $cpfBusca . '%');
        }

        // 2. Categoria
        if ($request->filled('categoria')) {
            $query->where('atl_categoria', $request->categoria);
        }

        // 3. Ano de Inscrição
        if ($request->filled('ano_inscricao')) {
            $query->where('atl_ano_insc', $request->ano_inscricao);
        }

        // 4. Sexo
        if ($request->filled('sexo')) {
            $query->where('atl_sexo', $request->sexo);
        }

        // 5. Time
        if ($request->filled('time_id')) {
            $query->where('atl_tim_id', $request->time_id);
        }

        // Obtém categorias para o filtro
        $categorias = Categoria::orderBy('cto_nome')->get();

        $times = [];
        if ($user->hasRole('Administrador')) {
            $times = Time::orderBy('tim_nome')->get();
        }

        $atletas = $query->paginate(10)->appends($request->all());

        return view('atletas.index', compact('atletas', 'categorias', 'times'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $times = [];
        if (auth()->user()->hasRole('Administrador')) {
            $times = Time::all();
        }
        $categorias = Categoria::all();
        return view('atletas.create', compact('times', 'categorias'));
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
            $request->merge(['atl_tim_id' => $time->tim_id]);

        }

        // Limpa CPF e RG antes de validar para garantir unicidade com o formato do banco (apenas números)
        $request->merge([
            'atl_cpf' => preg_replace('/[^0-9]/', '', $request->atl_cpf ?? ''),
            'atl_rg' => preg_replace('/[^0-9]/', '', $request->atl_rg ?? ''),
        ]);

        $request->validate([
            'atl_nome' => 'required|string|max:100',
            'atl_cpf' => 'nullable|string|max:11|unique:atletas,atl_cpf', // Adicionando unique
            'atl_rg' => 'nullable|string|max:10', // RG sem máscara
            'atl_celular' => 'nullable|string|max:11', // Celular sem máscara
            'atl_telefone' => 'nullable|string|max:10', // Telefone sem máscara
            'atl_email' => 'nullable|email|max:100',
            'atl_sexo' => 'nullable|in:M,F,O', // M-Masculino, F-Feminino, O-Outro
            'atl_dt_nasc' => 'nullable|date_format:Y-m-d', // Formato YYYY-MM-DD
            'atl_resg' => 'nullable|string|max:50',
            'atl_endereco' => 'nullable|string|max:255',
            'atl_numero' => 'nullable|string|max:10',
            'atl_bairro' => 'nullable|string|max:100',
            'atl_cidade' => 'nullable|string|max:100',
            'atl_estado' => 'nullable|string|max:2', // UF
            'atl_cep' => 'nullable|string|max:8', // CEP sem máscara
            'atl_categoria' => 'nullable|exists:categorias,cto_id',
            'atl_ano_insc' => 'nullable|integer|digits:4',
            'atl_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'atl_tim_id' => 'nullable|exists:times,tim_id',
        ]);

        // Prepara os dados, removendo 'atl_foto' para processar separadamente
        $data = $request->except(['atl_foto']);
        $data['atl_ativo'] = 1; // Garante que o atleta seja criado como ativo

        // Ajusta campos que vêm formatados ou precisam de pré-processamento
        $data['atl_cpf'] = preg_replace('/[^0-9]/', '', $data['atl_cpf'] ?? '');
        $data['atl_rg'] = preg_replace('/[^0-9]/', '', $data['atl_rg'] ?? '');
        $data['atl_celular'] = preg_replace('/[^0-9]/', '', $data['atl_celular'] ?? '');
        $data['atl_telefone'] = preg_replace('/[^0-9]/', '', $data['atl_telefone'] ?? '');
        $data['atl_cep'] = preg_replace('/[^0-9]/', '', $data['atl_cep'] ?? '');

        // --- Processamento do Upload da Foto ---
        if ($request->hasFile('atl_foto')) {
            $file = $request->file('atl_foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                // Salva o arquivo no disco 'atletas_fotos'
                $path = Storage::disk('atletas_fotos')->putFileAs('/', $file, $filename);
                Log::info("Foto do atleta '{$filename}' salva com sucesso no caminho: {$path} (Criação)");
                $data['atl_foto'] = $filename; // Adiciona o nome do arquivo aos dados
            } catch (\Exception $e) {
                Log::error("Erro ao salvar a foto do atleta durante a criação: " . $e->getMessage(), [
                    'filename' => $filename,
                    'file_original_name' => $file->getClientOriginalName(),
                    'disk' => 'atletas_fotos',
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Ocorreu um erro ao enviar a foto do atleta. Por favor, tente novamente.');
            }
        } else {
            $data['atl_foto'] = null; // Se não houver arquivo, define como null
        }
        // --- Fim do Processamento do Upload ---

        try {
            $atleta = Atleta::create($data); // Cria um novo atleta com os dados preparados

            // Verificar checkbox de cartão impresso (Ano Atual)
            if ($request->has('cartao_impresso_ano_atual')) {
                \App\Models\AtletaCartao::create([
                    'atc_atl_id' => $atleta->atl_id,
                    'atc_ano' => date('Y'),
                    'atc_impresso' => true,
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erro ao criar o atleta: " . $e->getMessage(), [
                'request_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao criar o atleta. Por favor, tente novamente.');
        }

        return redirect()->route('atletas.index')->with('success', 'Atleta cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atleta $atleta)
    {
        return view('atletas.show', compact('atleta'));
    }

    /**
     * Display the specified resource for printing.
     */
    public function print(Atleta $atleta)
    {
        return view('atletas.print', compact('atleta'));
    }

    /**
     * Mark the athlete card as printed for the current year.
     */
    public function markPrinted(Atleta $atleta)
    {
        // Se for administrador, marca como impresso
        if (auth()->user()->hasRole('Administrador')) {
            $anoAtual = date('Y');
            \App\Models\AtletaCartao::updateOrCreate(
                [
                    'atc_atl_id' => $atleta->atl_id,
                    'atc_ano' => $anoAtual,
                ],
                ['atc_impresso' => true]
            );
            return redirect()->back()->with('success', 'Cartão marcado como impresso com sucesso!');
        }

        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atleta $atleta)
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            if ($atleta->atl_tim_id != Time::where('tim_user_id', $user->id)->value('tim_id')) {
                abort(403);
            }
        }

        $times = [];
        if ($user->hasRole('Administrador')) {
            $times = Time::all();
        }
        $categorias = Categoria::all();

        return view('atletas.edit', compact('atleta', 'times', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atleta $atleta)
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $atleta->atl_tim_id != $time->tim_id) {
                abort(403);
            }
            $request->merge(['atl_tim_id' => $time->tim_id]);
        }
        $request->validate([
            'atl_nome' => 'required|string|max:100',
            'atl_cpf' => 'nullable|string|max:11',
            'atl_rg' => 'nullable|string|max:10',
            'atl_celular' => 'nullable|string|max:11',
            'atl_telefone' => 'nullable|string|max:10',
            'atl_email' => 'nullable|email|max:100',
            'atl_sexo' => 'nullable|in:M,F,O',
            'atl_dt_nasc' => 'nullable|date_format:Y-m-d',
            'atl_resg' => 'nullable|string|max:50',
            'atl_endereco' => 'nullable|string|max:255',
            'atl_numero' => 'nullable|string|max:10',
            'atl_bairro' => 'nullable|string|max:100',
            'atl_cidade' => 'nullable|string|max:100',
            'atl_estado' => 'nullable|string|max:2',
            'atl_cep' => 'nullable|string|max:8',
            'atl_categoria' => 'nullable|exists:categorias,cto_id',
            'atl_ano_insc' => 'nullable|integer|digits:4',
            'atl_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'atl_tim_id' => 'nullable|exists:times,tim_id',
        ]);

        $data = $request->except(['atl_foto']);

        // Ajusta campos que vêm formatados ou precisam de pré-processamento
        $data['atl_cpf'] = preg_replace('/[^0-9]/', '', $data['atl_cpf'] ?? '');
        $data['atl_rg'] = preg_replace('/[^0-9]/', '', $data['atl_rg'] ?? '');
        $data['atl_celular'] = preg_replace('/[^0-9]/', '', $data['atl_celular'] ?? '');
        $data['atl_telefone'] = preg_replace('/[^0-9]/', '', $data['atl_telefone'] ?? '');
        $data['atl_cep'] = preg_replace('/[^0-9]/', '', $data['atl_cep'] ?? '');


        // --- Processamento do Upload da Foto para ATUALIZAR ---
        if ($request->hasFile('atl_foto')) {
            $file = $request->file('atl_foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                // Se existe uma foto antiga, tenta deletá-la
                if ($atleta->atl_foto && Storage::disk('atletas_fotos')->exists($atleta->atl_foto)) {
                    Storage::disk('atletas_fotos')->delete($atleta->atl_foto);
                    Log::info("Foto antiga '{$atleta->atl_foto}' deletada com sucesso (Atualização do Atleta).");
                }

                // Salva o novo arquivo no disco 'atletas_fotos'
                $path = Storage::disk('atletas_fotos')->putFileAs('/', $file, $filename);
                Log::info("Nova foto do atleta '{$filename}' salva com sucesso no caminho: {$path} (Atualização)");
                $data['atl_foto'] = $filename; // Adiciona o nome do novo arquivo aos dados
            } catch (\Exception $e) {
                Log::error("Erro ao salvar/deletar a foto do atleta durante a atualização: " . $e->getMessage(), [
                    'filename' => $filename ?? 'N/A',
                    'file_original_name' => $file->getClientOriginalName() ?? 'N/A',
                    '' => 'atletas_fotos',
                    'old_foto' => $atleta->atl_foto,
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar a foto do atleta. Por favor, tente novamente.');
            }
        } else {
            // Se nenhum novo arquivo foi fornecido, e não há checkbox para remover, mantém o existente.
            // Se quiser permitir remover, adicione uma checkbox e ajuste esta lógica.
            unset($data['atl_foto']); // Não atualiza o campo 'atl_foto' no banco de dados se não houver novo upload
        }
        // --- Fim do Processamento do Upload para ATUALIZAR ---

        try {
            $atleta->update($data);

            // Atualizar/Criar status do cartão do ano atual
            $anoAtual = date('Y');
            $cartao = \App\Models\AtletaCartao::where('atc_atl_id', $atleta->atl_id)
                ->where('atc_ano', $anoAtual)
                ->first();

            if ($request->has('cartao_impresso_ano_atual')) {
                // Se marcado, cria se não existir ou garante que está true
                if ($cartao) {
                    $cartao->update(['atc_impresso' => true]);
                } else {
                    \App\Models\AtletaCartao::create([
                        'atc_atl_id' => $atleta->atl_id,
                        'atc_ano' => $anoAtual,
                        'atc_impresso' => true,
                    ]);
                }
            } else {
                // Se DESMARCADO, se existir registro, marca como false
                if ($cartao) {
                    $cartao->update(['atc_impresso' => false]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Erro ao atualizar o atleta com ID {$atleta->atl_id}: " . $e->getMessage(), [
                'atleta_id' => $atleta->atl_id,
                'request_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar o atleta. Por favor, tente novamente.');
        }

        return redirect()->route('atletas.index')->with('success', 'Atleta atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atleta $atleta)
    {
        try {
            // Se existe uma foto, tenta deletá-la antes de remover o registro
            if ($atleta->atl_foto && Storage::disk('atletas_fotos')->exists($atleta->atl_foto)) {
                Storage::disk('atletas_fotos')->delete($atleta->atl_foto);
                Log::info("Foto '{$atleta->atl_foto}' deletada do armazenamento durante a exclusão do atleta.");
            }

            $atleta->delete();
            Log::info("Atleta com ID {$atleta->atl_id} excluído com sucesso.");

        } catch (\Exception $e) {
            Log::error("Erro ao excluir o atleta com ID {$atleta->atl_id}: " . $e->getMessage(), [
                'atleta_id' => $atleta->atl_id,
                'foto_filename' => $atleta->atl_foto,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir o atleta. Por favor, tente novamente.');
        }

        return redirect()->route('atletas.index')->with('success', 'Atleta excluído com sucesso!');
    }

    /**
     * Inactivate the specified athlete.
     */
    public function inactivate(Atleta $atleta)
    {
        // Verificações de permissão
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            if ($atleta->atl_tim_id != Time::where('tim_user_id', $user->id)->value('tim_id')) {
                abort(403);
            }
        }

        $atleta->atl_ativo = !$atleta->atl_ativo;
        $atleta->save();

        $statusMessage = $atleta->atl_ativo ? 'ativado' : 'inativado';
        return redirect()->route('atletas.index')->with('success', "Atleta $statusMessage com sucesso!");
    }
}