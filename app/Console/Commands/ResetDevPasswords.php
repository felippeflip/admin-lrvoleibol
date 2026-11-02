<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetDevPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:reset-passwords 
                            {password=123456 : Nova senha a ser definida para os usuários}
                            {--role= : Filtrar por perfil específico (ex: Administrador, Juiz, ResponsavelTime)}
                            {--all : Incluir também usuários inativos (por padrão, apenas ATIVOS são alterados)}
                            {--force : Executa sem solicitar confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redefine a senha dos usuários ativos em ambiente de desenvolvimento (local) e lista nome, e-mail e perfil.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Trava estrita de segurança: apenas ambiente 'local'
        if (!app()->environment('local')) {
            $this->error('================================================================');
            $this->error(' BLOQUEADO: Este comando só pode ser executado em ambiente local!');
            $this->error(' Ambiente atual: ' . app()->environment());
            $this->error('================================================================');
            return self::FAILURE;
        }

        $password = $this->argument('password');
        $roleFilter = $this->option('role');
        $includeAll = $this->option('all');

        $query = User::with('roles')->orderBy('name');

        // Por padrão, filtra apenas usuários ATIVOS
        if (!$includeAll) {
            $query->where('active', true);
        }

        if ($roleFilter) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        $users = $query->get();
        $total = $users->count();

        if ($total === 0) {
            $this->warn('Nenhum usuário ' . (!$includeAll ? 'ativo ' : '') . 'encontrado' . ($roleFilter ? " com o perfil '{$roleFilter}'." : '.'));
            return self::SUCCESS;
        }

        $tipoFiltro = !$includeAll ? 'ATIVOS' : 'TODOS (ativos e inativos)';
        $this->info("Ambiente: LOCAL | Usuários ({$tipoFiltro}): {$total}");
        $this->info("Nova senha que será aplicada: '{$password}'");

        // 2. Confirmação interativa (a menos que use --force)
        if (!$this->option('force')) {
            if (!$this->confirm("Deseja realmente redefinir a senha de {$total} usuário(s) ({$tipoFiltro}) para '{$password}'?", true)) {
                $this->warn('Operação cancelada pelo usuário.');
                return self::SUCCESS;
            }
        }

        // 3. Atualização das senhas com Hash do Laravel
        $hashedPassword = Hash::make($password);
        User::whereIn('id', $users->pluck('id'))->update(['password' => $hashedPassword]);

        // 4. Montagem da tabela com Nome, E-mail e Perfil
        $tableData = $users->map(function ($user) {
            $roles = $user->roles->pluck('name')->toArray();

            // Adiciona identificadores extras se não estiverem nas roles
            if ($user->is_arbitro && !in_array('Juiz', $roles)) {
                $roles[] = 'Árbitro/Apontador';
            }
            if ($user->is_resp_time && !in_array('ResponsavelTime', $roles) && !in_array('ComissaoTecnica', $roles)) {
                $roles[] = 'Resp. Time';
            }

            $perfilStr = !empty($roles) ? implode(', ', $roles) : 'Sem Perfil';
            $statusStr = $user->active ? 'Ativo' : 'Inativo';

            return [
                'id'     => $user->id,
                'name'   => $user->name . ($user->apelido ? " ({$user->apelido})" : ''),
                'email'  => $user->email,
                'perfil' => $perfilStr,
                'status' => $statusStr,
            ];
        })->toArray();

        $this->newLine();
        $this->info('================================================================');
        $this->info("  SENHAS REDEFINIDAS COM SUCESSO! Senha padrão: '{$password}'");
        $this->info('================================================================');
        $this->newLine();

        $this->table(
            ['ID', 'Nome / Apelido', 'E-mail (Login)', 'Perfil / Roles', 'Status'],
            $tableData
        );

        $this->newLine();
        $this->comment("Total de contas prontas para acesso: {$total}");
        $this->comment("Dica: você pode logar com qualquer um dos e-mails acima usando a senha: {$password}");

        return self::SUCCESS;
    }
}

