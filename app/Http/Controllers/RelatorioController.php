<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\ComissaoTecnica;
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
        // ── DETECÇÃO MOBILE ─────────────────────────────────────────────────
        if ($this->isMobileView()) {
            return view('mobile.relatorios.index');
        }

        return view('relatorios.index');
    }

    /**
     * Relatório de Atletas por Time (Protocolo de Carteirinhas).
     */
    public function atletasPorTime(Request $request)
    {
        $query = Atleta::with(['time', 'categoria', 'cartoes'])
            ->where('atl_ativo', 1);

        // RBAC: Se não for administrador, filtra pelo time vinculado ao usuário
        if (!auth()->user()->hasRole('Administrador')) {
            $userTimeId = auth()->user()->time_id;
            
            if (!$userTimeId) {
                // Se o usuário não tem time vinculado e não é admin, retorna lista vazia
                $atletas = collect([]);
                $timesList = collect([]);
                return view($this->isMobileView() ? 'mobile.relatorios.atletas_por_time' : 'relatorios.atletas_por_time', compact('atletas', 'timesList'));
            }
            
            $query->where('atl_tim_id', $userTimeId);
            $timesList = Time::where('tim_id', $userTimeId)->get();
        } else {
            // Administrador pode filtrar por qualquer time
            if ($request->filled('time_id')) {
                $query->where('atl_tim_id', $request->time_id);
            }
            $timesList = Time::where('tim_status', 1)->orderBy('tim_nome')->get();
        }

        $atletas = $query->orderBy('atl_tim_id')
            ->orderBy('atl_nome')
            ->get()
            ->groupBy('atl_tim_id');

        // ── DETECÇÃO MOBILE ─────────────────────────────────────────────────
        if ($this->isMobileView()) {
            return view('mobile.relatorios.atletas_por_time', compact('atletas', 'timesList'));
        }

        return view('relatorios.atletas_por_time', compact('atletas', 'timesList'));
    }

    /**
     * Exporta o relatório de Atletas por Time para CSV/Excel.
     */
    public function exportAtletasPorTime(Request $request)
    {
        $query = Atleta::with(['time', 'categoria', 'cartoes'])
            ->where('atl_ativo', 1);

        // RBAC: Se não for administrador, filtra pelo time vinculado ao usuário
        if (!auth()->user()->hasRole('Administrador')) {
            $userTimeId = auth()->user()->time_id;
            if (!$userTimeId) {
                return redirect()->route('relatorios.index')->with('error', 'Usuário não vinculado a um time.');
            }
            $query->where('atl_tim_id', $userTimeId);
        } else {
            if ($request->filled('time_id')) {
                $query->where('atl_tim_id', $request->time_id);
            }
        }

        $atletas = $query->orderBy('atl_tim_id')
            ->orderBy('atl_nome')
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';'); // Melhor para Excel no Brasil
        
        // Inserir BOM para UTF-8 (Excel reconhecimento correto de acentos)
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Cabeçalho
        $csv->insertOne(['Time', 'Atleta', 'Registro LRV', 'Categoria', 'CPF', 'RG', 'Data Nasc.', 'Cartão Impresso']);

        foreach ($atletas as $atleta) {
            $csv->insertOne([
                $atleta->time->tim_nome ?? 'N/A',
                $atleta->atl_nome,
                $atleta->atl_resg ?? 'N/A',
                $atleta->categoria->cto_nome ?? 'N/A',
                $atleta->atl_cpf,
                $atleta->atl_rg,
                $atleta->atl_dt_nasc ? date('d/m/Y', strtotime($atleta->atl_dt_nasc)) : 'N/A',
                $atleta->cartaoImpresso() ? 'SIM' : 'NÃO',
            ]);
        }

        $filename = 'relatorio_atletas_por_time_' . date('Ymd_His') . '.csv';

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Relatório que exibe as tabelas (arquivos HTML) geradas.
     */
    public function tabelasGeradas()
    {
        $arquivos = [];
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tabelas')) {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files('tabelas');
            
            foreach ($files as $file) {
                $pathInfo = pathinfo($file);
                
                // Só exibe arquivos html
                if (isset($pathInfo['extension']) && strtolower($pathInfo['extension']) === 'html') {
                    $arquivos[] = [
                        'nome' => $pathInfo['basename'],
                        'caminho' => asset('storage/' . $file),
                        'tamanho' => self::formatSizeUnits(\Illuminate\Support\Facades\Storage::disk('public')->size($file)),
                        'data_modificacao' => \Carbon\Carbon::createFromTimestamp(\Illuminate\Support\Facades\Storage::disk('public')->lastModified($file))->format('d/m/Y H:i:s'),
                        'timestamp' => \Illuminate\Support\Facades\Storage::disk('public')->lastModified($file)
                    ];
                }
            }
        }

        // Ordenar do mais recente para o mais antigo
        usort($arquivos, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return view('relatorios.tabelas_geradas', compact('arquivos'));
    }

    /**
     * Relatório de Comissão Técnica por Time.
     */
    public function comissaoPorTime(Request $request)
    {
        $query = ComissaoTecnica::with(['time'])
            ->where('status', 1);

        // RBAC: Se não for administrador, filtra pelo time vinculado ao usuário
        if (!auth()->user()->hasRole('Administrador')) {
            $userTimeId = auth()->user()->time_id;
            
            if (!$userTimeId) {
                // Se o usuário não tem time vinculado e não é admin, retorna lista vazia
                $comissao = collect([]);
                $timesList = collect([]);
                return view($this->isMobileView() ? 'mobile.relatorios.comissao_por_time' : 'relatorios.comissao_por_time', compact('comissao', 'timesList'));
            }
            
            $query->where('time_id', $userTimeId);
            $timesList = Time::where('tim_id', $userTimeId)->get();
        } else {
            // Administrador pode filtrar por qualquer time
            if ($request->filled('time_id')) {
                $query->where('time_id', $request->time_id);
            }
            $timesList = Time::where('tim_status', 1)->orderBy('tim_nome')->get();
        }

        $comissao = $query->orderBy('time_id')
            ->orderBy('nome')
            ->get()
            ->groupBy('time_id');

        // ── DETECÇÃO MOBILE ─────────────────────────────────────────────────
        if ($this->isMobileView()) {
            return view('mobile.relatorios.comissao_por_time', compact('comissao', 'timesList'));
        }

        return view('relatorios.comissao_por_time', compact('comissao', 'timesList'));
    }

    /**
     * Exporta o relatório de Comissão Técnica para CSV/Excel.
     */
    public function exportComissaoPorTime(Request $request)
    {
        $query = ComissaoTecnica::with(['time'])
            ->where('status', 1);

        // RBAC: Se não for administrador, filtra pelo time vinculado ao usuário
        if (!auth()->user()->hasRole('Administrador')) {
            $userTimeId = auth()->user()->time_id;
            if (!$userTimeId) {
                return redirect()->route('relatorios.index')->with('error', 'Usuário não vinculado a um time.');
            }
            $query->where('time_id', $userTimeId);
        } else {
            if ($request->filled('time_id')) {
                $query->where('time_id', $request->time_id);
            }
        }

        $membros = $query->orderBy('time_id')
            ->orderBy('nome')
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Cabeçalho
        $csv->insertOne(['Time', 'Nome', 'Função', 'Registro LRV', 'CPF', 'RG', 'Celular', 'Email', 'Cartão Impresso']);

        foreach ($membros as $membro) {
            $csv->insertOne([
                $membro->time->tim_nome ?? 'N/A',
                $membro->nome,
                $membro->funcao ?? 'N/A',
                $membro->registro_lrv ?? 'N/A',
                $membro->cpf,
                $membro->rg,
                $membro->celular,
                $membro->email,
                $membro->cartaoImpresso() ? 'SIM' : 'NÃO',
            ]);
        }

        $filename = 'relatorio_comissao_por_time_' . date('Ymd_His') . '.csv';

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Helper para formatar o tamanho dos arquivos
     */
    private static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
