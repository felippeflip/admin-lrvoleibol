<?php

namespace App\Http\Controllers;

use App\Models\Atleta; // Certifique-se de usar 'Atleta' com 'A' maiúsculo para o modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para manipulação de arquivos
use Illuminate\Support\Str; // Para gerar nomes de arquivo únicos
use Illuminate\Support\Facades\Log; // Para logs de erro

class AtletaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $atletas = Atleta::paginate(10); // Pagina 10 atletas por página
        return view('atletas.index', compact('atletas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('atletas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'atl_nome' => 'required|string|max:100',
            'atl_cpf' => 'nullable|string|max:11', // CPF sem máscara
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
            'atl_categoria' => 'nullable|string|max:50',
            'atl_ano_insc' => 'nullable|integer|digits:4',
            'atl_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // Prepara os dados, removendo 'atl_foto' para processar separadamente
        $data = $request->except(['atl_foto']);

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
            Atleta::create($data); // Cria um novo atleta com os dados preparados
        } catch (\Exception $e) {
            Log::error("Erro ao criar o atleta: " . $e->getMessage(), [
                'request_data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao criar o atleta. Por favor, tente novamente.');
        }

        return redirect()->route('atletas.index')->with('success', 'Atleta adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atleta $atleta)
    {
        return view('atletas.show', compact('atleta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atleta $atleta)
    {
        return view('atletas.edit', compact('atleta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atleta $atleta)
    {
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
            'atl_categoria' => 'nullable|string|max:50',
            'atl_ano_insc' => 'nullable|integer|digits:4',
            'atl_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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
}