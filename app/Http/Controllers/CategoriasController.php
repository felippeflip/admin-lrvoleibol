<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use App\Models\Wp_Terms;
use App\Models\Categoria;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cto_idade_maxima' => 'nullable|integer|min:1',
        ]);


        // Inserir dados na tabela wp_terms
        $wpTerm = Wp_Terms::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
        ]);

        // Inserir dados na tabela wp_term_taxonomy
        Wp_Term_Taxonomy::create([
            'term_id' => $wpTerm->term_id,
            'taxonomy' => 'event_listing_category',
            'description' => $request->input('description') ?? '',
            'parent' => 0,
            'count' => 0,
        ]);

        Categoria::create([
            'cto_nome' => $request->input('name'),
            'cto_slug' => $request->input('slug'),
            'cto_term_tx_id' => $wpTerm->term_id,
            'cto_descricao' => $request->input('description') ?? '',
            'cto_idade_maxima' => $request->input('cto_idade_maxima'),
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso');
    }

    public function edit($id)
    {

        $categoria = Categoria::findOrFail($id);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cto_idade_maxima' => 'nullable|integer|min:1',
        ]);

        $request->description ? empty($request->description) : ' ';

        $categoria = Categoria::findOrFail($id);

        $categoria->update([
            'cto_nome' => $request->input('name'),
            'cto_slug' => $request->input('slug'),
            'cto_descricao' => $request->input('description') ?? '',
            'cto_idade_maxima' => $request->input('cto_idade_maxima'),
        ]);

        $wpTermTaxonomy = Wp_Term_Taxonomy::findOrFail($categoria->cto_term_tx_id);
        $wpTerm = $wpTermTaxonomy->term;

        // Atualize os dados de wp_terms
        $wpTerm->update([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
        ]);

        // Garanta que o campo description não seja nulo
        $description = $request->input('description') ?? '';

        // Atualize os dados de wp_term_taxonomy
        $wpTermTaxonomy->update([
            'description' => $description,

        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso');
    }

    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);

            $taxonomy = Wp_Term_Taxonomy::findOrFail($categoria->cto_term_tx_id);

            // Primeiro, deletar o registro de wp_term_taxonomy
            $taxonomy->delete();

            // Depois, deletar o registro correspondente em wp_terms
            Wp_Terms::where('term_id', $taxonomy->term_id)->delete();

            $categoria->delete();

        } catch (\Exception $e) {
            logger()->error('Erro ao deletar categoria: ' . $e->getMessage());
        }

        // Redirecionar com mensagem de sucesso


        return redirect()->route('categorias.index')->with('success', 'Categoria deletada com sucesso');
    }
}
