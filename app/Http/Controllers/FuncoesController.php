<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use Illuminate\Support\Facades\Storage;

class FuncoesController extends Controller
{
    public function showImport()
    {
        $eventCategorys = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();
        return view('funcoes.showimportresultado', compact('eventCategorys'));
    }

    public function upload(Request $request)
    {
            // Validação dos dados
            $request->validate([
                'category' => 'required|string',
                'html' => 'required|file|mimes:html|max:2048', // Max 2 MB, aceitando apenas HTML
            ]);

    try {
            // Processar o upload do arquivo
            $htmlFile = $request->file('html');
            $categoryName = $request->input('category');

            // Formatando o nome do arquivo
            $fileName = strtolower(str_replace(' ', '-', $categoryName)) . '.html';

            // Definir o caminho para o diretório de uploads
            $uploadPath = 'public/uploads';

            // Verificar se o diretório de uploads existe, caso contrário, criar
            if (!Storage::exists($uploadPath)) {
            Storage::makeDirectory($uploadPath);
            }

            // Verificar se já existe um arquivo com o mesmo nome e excluí-lo
            if (Storage::exists($uploadPath . '/' . $fileName)) {
                Storage::delete($uploadPath . '/' . $fileName);
            }

            // Mover o arquivo para o diretório de uploads com o novo nome
            $path = $htmlFile->storeAs($uploadPath, $fileName);

            // Gerar a URL pública para o arquivo
            $fileUrl = Storage::url($path);

            return redirect()->route('resultados.showImportForm')
                         ->with('success', 'Arquivo HTML importado com sucesso!')
                         ->with('fileUrl', $fileUrl);

            } catch (\Exception $e) {
            // Em caso de erro, redirecionar de volta com uma mensagem de erro
            return redirect()->route('resultados.showImportForm')
                         ->with('error', 'Ocorreu um erro ao importar o arquivo HTML. Por favor, tente novamente.');
            }
    }
}
