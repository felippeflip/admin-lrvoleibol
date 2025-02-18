<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FuncoesController extends Controller
{
    public function showImport()
    {
        $eventCategorys = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();
        return view('funcoes.showimportresultado', compact('eventCategorys'));
    }

    public function upload(Request $request)
{
    Log::info('Iniciando upload de arquivo HTML');

    // Validação dos dados
    $request->validate([
        'category' => 'required|string',
        'html' => 'required|file|mimes:html|max:2048', // Max 2 MB, aceitando apenas HTML
    ]);

    try {
        $htmlFile = $request->file('html');
        $categoryName = $request->input('category');

        // Formatando o nome do arquivo
        $fileName = strtolower(str_replace(' ', '-', $categoryName)) . '.html';

        $uploadPath = 'uploads';

        // Verificar e criar diretório de uploads
        if (!Storage::disk('public')->exists($uploadPath)) {
            Storage::disk('public')->makeDirectory($uploadPath);
        }

        // Excluir arquivo existente com o mesmo nome
        if (Storage::disk('public')->exists($uploadPath . '/' . $fileName)) {
            Storage::disk('public')->delete($uploadPath . '/' . $fileName);
        }

        // Mover o arquivo para o diretório de uploads
        $path = $htmlFile->storeAs($uploadPath, $fileName, 'public');

        // Gerar a URL pública para o arquivo
        $fileUrl = asset('storage/' . $path);

        Log::info('Arquivo HTML importado com sucesso', ['fileUrl' => $fileUrl]);

        return redirect()->route('resultados.showImportForm')
                         ->with('success', 'Arquivo HTML importado com sucesso!')
                         ->with('fileUrl', $fileUrl);

    } catch (\Exception $e) {
        Log::error('Erro ao importar arquivo HTML', ['exception' => $e->getMessage()]);

        return redirect()->route('resultados.showImportForm')
                         ->with('error', 'Ocorreu um erro ao importar o arquivo HTML. Por favor, tente novamente.');
    }
}
}
