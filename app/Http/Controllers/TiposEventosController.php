<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use App\Models\Wp_Terms;
use App\Models\Campeonato;
use Illuminate\Support\Facades\Log;


class TiposEventosController extends Controller
{
    public function index(Request $request)
    {
        $query = Campeonato::query();

        // Filtro por Nome
        if ($request->filled('search')) {
            $query->where('cpo_nome', 'like', '%' . $request->search . '%');
        }

        // Filtro por Ano
        if ($request->filled('ano')) {
            $query->where('cpo_ano', $request->ano);
        }

        // Filtro por Status
        if ($request->filled('ativo')) {
            if ($request->ativo == '1') {
                $query->where('cpo_ativo', true);
            } elseif ($request->ativo == '0') {
                $query->where('cpo_ativo', false);
            }
        }

        $campeonatos = $query->orderBy('cpo_id', 'desc')->paginate(10)->appends($request->all());
   
        return view('eventos.index', compact('campeonatos'));
    }

    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cpo_nome' =>       'required|string|max:50',
            'slug' =>           'required|string|max:50',
            'cpo_ano' =>        'required|integer|min:2000',
            'cpo_dt_inicio' =>  'required|date',
            'cpo_dt_fim' =>     'required|date|after_or_equal:cpo_dt_inicio',
        ]);


        try {

        // Inserir dados na tabela wp_terms
        $wpTerm = Wp_Terms::create([
            'name' => $request->input('cpo_nome'),
            'slug' => $request->input('slug'),
        ]);

        // Inserir dados na tabela wp_term_taxonomy
        Wp_Term_Taxonomy::create([
            'term_id'       => $wpTerm->term_id,
            'taxonomy'      => 'event_listing_type',
            'description'   => $request->input('cpo_nome') ?? '',
            'parent'        => 0,
            'count'         => 0,
        ]);

        // Inserir dados na tabela campeonatos
        Campeonato::create([
            'cpo_nome'         => $request->input('cpo_nome'),
            'cpo_term_tx_id'   => $wpTerm->term_id,
            'cpo_ano'          => $request->input('cpo_ano'),
            'cpo_dt_inicio'    => $request->input('cpo_dt_inicio'),
            'cpo_dt_fim'       => $request->input('cpo_dt_fim'),
            'cpo_ativo'        => true,
        ]);

    } catch (\Exception $e) {
             Log::error('Erro ao criar campeonato: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() // Exemplo: se você quiser registrar o ID do usuário logado
    ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao criar campeonato: ' . $e->getMessage()]);
    }

        // Redirecionar para a lista de eventos com uma mensagem de sucesso

        return redirect()->route('eventos.index')->with('success', 'Campeonato criado com sucesso');
    }

    public function edit($id)
    {

        $campeonato = Campeonato::findOrFail($id);


        $wpTermTaxonomy = Wp_Term_Taxonomy::with('term')->findOrFail($campeonato->cpo_term_tx_id);
        $wpTerm = $wpTermTaxonomy->term;

        return view('eventos.edit', compact('campeonato' , 'wpTermTaxonomy', 'wpTerm'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cpo_nome' => 'required|string|max:50',
            'cpo_ano' => 'required|integer|min:2000|max:' . date('Y'),
            'cpo_dt_inicio' => 'required|date',
            'cpo_dt_fim' => 'required|date|after_or_equal:cpo_dt_inicio',
        ]);

       $campeonato = Campeonato::findOrFail($id);

        try {

        // Atualize os dados do campeonato
        $campeonato->update([
            'cpo_nome' => $request->input('cpo_nome'),
            'cpo_ano' => $request->input('cpo_ano'),
            'cpo_dt_inicio' => $request->input('cpo_dt_inicio'),
            'cpo_dt_fim' => $request->input('cpo_dt_fim'),
            'cpo_ativo' => $request->boolean('cpo_ativo'),
        ]);

        $wpTermTaxonomy = Wp_Term_Taxonomy::findOrFail($campeonato->cpo_term_tx_id);
        $wpTerm = $wpTermTaxonomy->term;

        // Atualize os dados de wp_terms
        $wpTerm->update([
            'name' => $request->input('cpo_nome'),  
            'slug' =>  $request->input('slug'),
        ]);

        // Garanta que o campo description não seja nulo
        $description = $request->input('cpo_nome') ?? '';

         // Atualize os dados de wp_term_taxonomy
         $wpTermTaxonomy->update([
            'description' => $description,

        ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar campeonato: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar campeonato: ' . $e->getMessage()]);
        }

        return redirect()->route('eventos.index')->with('success', 'Evento atualizado com sucesso');
    }

    public function destroy($id)
    {
        try {
            $campeonato = Campeonato::findOrFail($id);

            // Verifica se existem equipes vinculadas a este campeonato
            // Assumindo que a relação se chama 'equipes' no model Campeonato
            if ($campeonato->equipes()->exists()) {
                return redirect()->route('eventos.index')
                    ->withErrors(['error' => 'Não é possível excluir este campeonato pois existem equipes vinculadas a ele.']);
            }

            // Armazena IDs relacionados para exclusão posterior
            $termTaxonomyId = $campeonato->cpo_term_tx_id;

            // Exclui o campeonato
            $campeonato->delete();

            // Exclui dados relacionados do WordPress (Wp_Term_Taxonomy e Wp_Terms)
            if ($termTaxonomyId) {
                $taxonomy = Wp_Term_Taxonomy::find($termTaxonomyId);
                if ($taxonomy) {
                    $termId = $taxonomy->term_id;
                    $taxonomy->delete();
                    
                    if ($termId) {
                         Wp_Terms::where('term_id', $termId)->delete();
                    }
                }
            }

            return redirect()->route('eventos.index')->with('success', 'Evento deletado com sucesso');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir campeonato: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('eventos.index')->withErrors(['error' => 'Erro ao excluir campeonato.']);
        }
    }
}
