<?php

namespace App\Jobs;

use App\Models\PessoaModel;
use App\Services\NotificacaoService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificarUsuariosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $idPessoa;
    protected $dadosNotificacao;

    /**
     * Create a new job instance.
     *
     * @param PessoaModel $pessoa
     * @return void
     */
    public function __construct(int $idPessoa, array $dadosNotificacao)
    {
        $this->idPessoa = $idPessoa;
        $this->dadosNotificacao = $dadosNotificacao;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $notificacaoService = new NotificacaoService();
            $notificacaoService->enviarNotificacao($this->idPessoa, $this->dadosNotificacao);
        } catch (Exception $e) {
            Log::error('Erro ao enviar notificação por e-mail: ' . $e->getMessage());
            throw $e;
        }
    }
}
