<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Time;
use Illuminate\Http\Request;
use League\Csv\Writer;
use SplTempFileObject;

class RelatorioController extends Controller
{
    /**
     * Exibe a lista de relatórios disponíveis.
     */
    public function index()
    {
        return view('relatorios.index');
    }

    /**
     * Relatório de Atletas por Time (Protocolo de Carteirinhas).
     */
    public function atletasPorTime(Request $request)
    {
        $query = Atleta::with(['time', 'categoria'])
            ->where('atl_ativo', 1);

        if ($request->filled('time_id')) {
            $query->where('atl_tim_id', $request->time_id);
        }

        $atletas = $query->orderBy('atl_tim_id')
            ->orderBy('atl_nome')
            ->get()
            ->groupBy('atl_tim_id');

        $timesList = Time::where('tim_status', 1)->orderBy('tim_nome')->get();

        return view('relatorios.atletas_por_time', compact('atletas', 'timesList'));
    }

    /**
     * Exporta o relatório de Atletas por Time para CSV/Excel.
     */
    public function exportAtletasPorTime(Request $request)
    {
        $query = Atleta::with(['time', 'categoria'])
            ->where('atl_ativo', 1);

        if ($request->filled('time_id')) {
            $query->where('atl_tim_id', $request->time_id);
        }

        $atletas = $query->orderBy('atl_tim_id')
            ->orderBy('atl_nome')
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';'); // Melhor para Excel no Brasil
        
        // Inserir BOM para UTF-8 (Excel reconhecimento correto de acentos)
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Cabeçalho
        $csv->insertOne(['Time', 'Atleta', 'Registro LRV', 'Categoria', 'CPF', 'RG', 'Data Nasc.']);

        foreach ($atletas as $atleta) {
            $csv->insertOne([
                $atleta->time->tim_nome ?? 'N/A',
                $atleta->atl_nome,
                $atleta->atl_resg ?? 'N/A',
                $atleta->categoria->cto_nome ?? 'N/A',
                $atleta->atl_cpf,
                $atleta->atl_rg,
                $atleta->atl_dt_nasc ? date('d/m/Y', strtotime($atleta->atl_dt_nasc)) : 'N/A',
            ]);
        }

        $filename = 'relatorio_atletas_por_time_' . date('Ymd_His') . '.csv';

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
