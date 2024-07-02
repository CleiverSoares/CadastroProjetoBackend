<?php

namespace App\Jobs;

use App\Models\PessoaModel;
use App\Services\NotificacaoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;

class EnviarNotificaoEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pessoa;
    protected $dadosNotificacao;

    /**
     * Create a new job instance.
     *
     * @param PessoaModel $pessoa
     * @param array $dadosNotificacao
     * @return void
     */
    public function __construct(PessoaModel $pessoa, array $dadosNotificacao)
    {
        $this->pessoa = $pessoa;
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
            $notificacaoService->enviarNotificacaoEmail($this->pessoa, $this->dadosNotificacao);
        } catch (Exception $e) {
            Log::error('Erro ao enviar notificação por e-mail: ' . $e->getMessage());
            throw $e;
        }
    }
}
