<?php

namespace App\Http\Controllers;

use App\Models\Time; // Importe o modelo Time
use App\Models\User; // Importe o modelo User se necessário
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importe o Facade Storage
use Illuminate\Support\Str; // Importe Str para gerar nomes de arquivo únicos
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Time::with('user');

        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $query->where('tim_user_id', $user->id);
        }

        // Filters
        // Nome
        if ($request->filled('tim_nome')) {
            $query->where('tim_nome', 'like', '%' . $request->tim_nome . '%');
        }

        // Status

        if ($request->filled('status')) {
            if ($request->status !== 'todos') {
                $query->where('tim_status', $request->status);
            }
        } else {
            $query->where('tim_status', 1);
        }

        // Responsável (Nome do Usuário Responsável)
        if ($request->filled('responsavel')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->responsavel . '%');
            });
        }

        $times = $query->paginate(10);
        return view('times.index', compact('times'));
    }

    /**
     * Inactivate/Activate the specified resource.
     */
    public function inactivate(Time $time)
    {
        $time->tim_status = !$time->tim_status;
        $time->save();

        $status = $time->tim_status ? 'ativado' : 'desativado';
        return redirect()->back()->with('success', "Time $status com sucesso!");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso não autorizado.');
        }

        // Buscando apenas usuários que possuem a ROLE de Responsável de Time
        $users = User::role('ResponsavelTime')->get();

        // Retorna a view para criar um novo time
        return view('times.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso não autorizado.');
        }

        // remover caracteres não numéricos do CNPJ
        if ($request->has('tim_cnpj')) {
            $request['tim_cnpj'] = removeSpecialCharsFromCNPJ($request->tim_cnpj);
        }

        $request->validate([
            'tim_user_id' => 'nullable|integer',
            'tim_registro' => 'nullable|integer',
            'tim_cnpj' => 'nullable|string|max:14',
            'tim_nome' => 'required|string|max:100',
            'tim_nome_abre' => 'nullable|string|max:20',
            'tim_sigla' => 'nullable|string|max:10',
            'tim_endereco' => 'nullable|string|max:50',
            'tim_numero' => 'nullable|string|max:10',
            'tim_bairro' => 'nullable|string|max:50',
            'tim_cidade' => 'nullable|string|max:50',
            'tim_uf' => 'nullable|string|max:2',
            'tim_cep' => 'nullable|string|max:50',
            'tim_telefone' => 'nullable|string|max:20',
            'tim_celular' => 'nullable|string|max:50',
            'tim_email' => 'nullable|email|max:50',
            'tim_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Regra para imagem
            'tim_responsavel' => 'nullable|string|max:50',
            'new_user_name' => 'nullable|required_with:new_user_email|string|max:255',
            'new_user_email' => 'nullable|required_with:new_user_name|email|unique:users,email',
            'new_user_password' => 'nullable|required_with:new_user_name|min:8',
        ]);

        $data = $request->except(['tim_logo', 'new_user_name', 'new_user_email', 'new_user_password']); // Inicia um array com todos os dados da requisição

        // --- Lógica para criar ou vincular usuário ---
        if ($request->filled('new_user_name')) {
            $user = User::create([
                'name' => $request->new_user_name,
                'email' => $request->new_user_email,
                'password' => Hash::make($request->new_user_password),
                'is_resp_time' => true,
            ]);
            $user->assignRole('ResponsavelTime');
            $data['tim_user_id'] = $user->id;
        } elseif ($request->filled('tim_user_id')) {
            $user = User::find($request->tim_user_id);
            if ($user) {
                $user->assignRole('ResponsavelTime');
                if (!$user->is_resp_time) {
                    $user->is_resp_time = true;
                    $user->save();
                }
            }
        }

        // --- Processamento do Upload da Logo para CRIAR ---
        if ($request->hasFile('tim_logo')) {
            $file = $request->file('tim_logo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                // Salva o arquivo no disco 'times_logos'
                $path = Storage::disk('times_logos')->putFileAs('/', $file, $filename);
                Log::info("Logo do time '{$filename}' salva com sucesso no caminho: {$path} (Criação)");
                $data['tim_logo'] = $filename; // Adiciona o nome do arquivo gerado ao array de dados
            } catch (\Exception $e) {
                Log::error("Erro ao salvar a logo do time durante a criação: " . $e->getMessage(), [
                    'filename' => $filename,
                    'file_original_name' => $file->getClientOriginalName(),
                    'disk' => 'times_logos',
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Ocorreu um erro ao enviar a logo. Por favor, tente novamente.');
            }
        } else {
            $data['tim_logo'] = null; // Se não houver arquivo, define 'tim_logo' como null
        }
        // --- Fim do Processamento do Upload para CRIAR ---

        try {
            Time::create($data); // Cria um novo time com os dados preparados
        } catch (\Exception $e) {
            Log::error("Erro ao criar o time: " . $e->getMessage(), [
                'request_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao criar o time. Por favor, tente novamente.');
        }

        return redirect()->route('times.index')->with('success', 'Time adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Time $time)
    {
        // Retorna a view para exibir detalhes de um time específico
        return view('times.show', compact('time'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Time $time)
    {
        // Verifica permissão: Admin ou Dono do Time
        if (!auth()->user()->hasRole('Administrador') && $time->tim_user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Buscando apenas usuários que possuem a ROLE de Responsável de Time
        $users = User::role('ResponsavelTime')->get();

        // Retorna a view para editar um time específico
        return view('times.edit', compact('time', 'users')); // Passa $users para a view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Time $time)
    {
        // Verifica permissão: Admin ou Dono do Time
        if (!auth()->user()->hasRole('Administrador') && $time->tim_user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // remover caracteres não numéricos do CNPJ
        if ($request->has('tim_cnpj')) {
            $request['tim_cnpj'] = removeSpecialCharsFromCNPJ($request->tim_cnpj);
        }


        $request->validate([
            'tim_user_id' => 'nullable|integer',
            'tim_registro' => 'nullable|integer',
            'tim_cnpj' => 'nullable|string|max:14',
            'tim_nome' => 'required|string|max:100',
            'tim_nome_abre' => 'nullable|string|max:20',
            'tim_sigla' => 'nullable|string|max:10',
            'tim_endereco' => 'nullable|string|max:50',
            'tim_numero' => 'nullable|string|max:10',
            'tim_bairro' => 'nullable|string|max:50',
            'tim_cidade' => 'nullable|string|max:50',
            'tim_uf' => 'nullable|string|max:2',
            'tim_cep' => 'nullable|string|max:50',
            'tim_telefone' => 'nullable|string|max:20',
            'tim_celular' => 'nullable|string|max:50',
            'tim_email' => 'nullable|email|max:50',
            // A regra para 'tim_logo' agora é 'image' apenas se um arquivo for fornecido
            'tim_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tim_responsavel' => 'nullable|string|max:50',
        ]);

        // Inicia um array com todos os dados da requisição, exceto 'tim_logo'
        // IMPORTANTE: Se não for admin, removemos 'tim_user_id' do request para garantir que não mudem o dono
        if (!auth()->user()->hasRole('Administrador')) {
            $request->request->remove('tim_user_id');
        }

        $data = $request->except(['tim_logo']);

        // --- Lógica para vincular usuário na atualização ---
        if ($request->filled('tim_user_id') && auth()->user()->hasRole('Administrador')) {
            $user = User::find($request->tim_user_id);
            if ($user) {
                $user->assignRole('ResponsavelTime');
                if (!$user->is_resp_time) {
                    $user->is_resp_time = true;
                    $user->save();
                }
            }
        }

        // --- Processamento do Upload da Logo para ATUALIZAR ---
        if ($request->hasFile('tim_logo')) {
            $file = $request->file('tim_logo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                // Se existe um logo antigo, tenta deletá-lo
                if ($time->tim_logo && Storage::disk('times_logos')->exists($time->tim_logo)) {
                    Storage::disk('times_logos')->delete($time->tim_logo);
                    Log::info("Logo antiga '{$time->tim_logo}' deletada com sucesso (Atualização).");
                }

                // Salva o novo arquivo no disco 'times_logos'
                $path = Storage::disk('times_logos')->putFileAs('/', $file, $filename);
                Log::info("Novo logo do time '{$filename}' salvo com sucesso no caminho: {$path} (Atualização)");
                $data['tim_logo'] = $filename; // Adiciona o nome do novo arquivo aos dados
            } catch (\Exception $e) {
                Log::error("Erro ao salvar/deletar a logo do time durante a atualização: " . $e->getMessage(), [
                    'filename' => $filename ?? 'N/A', // Se a exceção ocorrer antes de $filename ser definido
                    'file_original_name' => $file->getClientOriginalName() ?? 'N/A',
                    'disk' => 'times_logos',
                    'old_logo' => $time->tim_logo,
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar a logo. Por favor, tente novamente.');
            }
        } else {
            // Se nenhum novo arquivo foi fornecido, verifica se o campo 'tim_logo' não está presente na requisição
            // Isso indica que o usuário não selecionou um novo arquivo e não removeu o existente via checkbox (que não temos)
            // Ou seja, o logo existente deve ser mantido
            // Se você adicionar uma checkbox para 'remover logo', esta lógica precisará ser ajustada.
            unset($data['tim_logo']); // Não atualiza o campo 'tim_logo' no banco de dados
        }
        // --- Fim do Processamento do Upload para ATUALIZAR ---

        try {
            // Atualiza o time com os dados preparados
            $time->update($data);
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar o time: " . $e->getMessage(), [
                'time_id' => $time->tim_id,
                'request_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar o time. Por favor, tente novamente.');
        }

        return redirect()->route('times.index')->with('success', 'Time atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Time $time)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acesso não autorizado.');
        }
        try {
            DB::beginTransaction();

            // Verifica se o time possui equipes
            $equipes = \App\Models\Equipe::where('eqp_time_id', $time->tim_id)->get();
            $hasActiveChampionships = false;

            // Verifica se alguma equipe está em campeonato
            foreach ($equipes as $equipe) {
                if ($equipe->campeonatos()->count() > 0) {
                    $hasActiveChampionships = true;
                    break;
                }
            }

            if ($hasActiveChampionships) {
                // Se houver erro de lógica (não deve apagar), faz rollback só por precaução, 
                // embora não tenhamos feito nada ainda.
                DB::rollBack();
                return redirect()->back()->with('error', 'Este time não pode ser excluído pois possui equipes inscritas em campeonatos. Remova-as dos campeonatos antes de excluir.');
            }

            // Exclui as equipes associadas (Cascata Manual)
            foreach ($equipes as $equipe) {
                $equipe->delete();
            }

            // Excluir Atletas associados
            $atletas = \App\Models\Atleta::where('atl_tim_id', $time->tim_id)->get();
            foreach ($atletas as $atleta) {
                // Se precisar deletar foto do atleta:
                if ($atleta->atl_foto && Storage::disk('atletas_fotos')->exists($atleta->atl_foto)) {
                    Storage::disk('atletas_fotos')->delete($atleta->atl_foto);
                }
                $atleta->delete();
            }

            // Excluir Ginásios associados (Se houver)
            // Cuidado: Se o ginásio estiver em uso por Jogos, isso pode falhar.
            // Vamos tentar deletar. Se falhar, o catch pega.
            $ginasios = \App\Models\Ginasio::where('gin_tim_id', $time->tim_id)->get();
            foreach ($ginasios as $ginasio) {
                $ginasio->delete();
            }

            // Se existe um logo, tenta deletá-lo antes de remover o registro do banco de dados
            if ($time->tim_logo && Storage::disk('times_logos')->exists($time->tim_logo)) {
                Storage::disk('times_logos')->delete($time->tim_logo);
                Log::info("Logo '{$time->tim_logo}' deletada do armazenamento durante a exclusão do time.");
            }

            // Deleta o time do banco de dados
            $time->delete();

            DB::commit();

            Log::info("Time com ID {$time->tim_id} excluído com sucesso.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro ao excluir o time com ID {$time->tim_id}: " . $e->getMessage(), [
                'time_id' => $time->tim_id,
                'logo_filename' => $time->tim_logo,
                'trace' => $e->getTraceAsString(),
            ]);

            // Verifica se o erro é de constraint violation
            if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return redirect()->back()->with('error', 'Não foi possível excluir o time pois existem registros vinculados (Ex: Jogos no Ginásio, etc) que impedem a exclusão.');
            }

            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir o time. Por favor, tente novamente.');
        }

        // Redireciona para a página de listagem com uma mensagem de sucesso
        return redirect()->route('times.index')->with('success', 'Time excluído com sucesso!');
    }
}