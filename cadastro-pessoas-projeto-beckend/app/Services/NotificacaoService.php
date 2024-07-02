<?php

namespace App\Services;

use App\Jobs\EnviarNotificaoEmailJob;
use App\Jobs\NotificarUsuariosJob;
use App\Models\PessoaModel;
use App\Services\External\MitteProService;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificacaoService
{
    private $mitteProService;

    public function processarNotificacao(int $idPessoa, array $dadosNotificacao)
    {
        NotificarUsuariosJob::dispatch($idPessoa, $dadosNotificacao)->onQueue('notificacoes');
    }

    public function enviarNotificacao(int $idPessoa, array $dadosNotificacao)
    {
        $pessoa = PessoaModel::find($idPessoa);

        EnviarNotificaoEmailJob::dispatch($pessoa, $dadosNotificacao)->onQueue('notificacoes');
    }

    public function enviarNotificacaoEmail($pessoa, $dadosNotificacao)
    {
        try {
            $this->mitteProService = new MitteProService();
            $assunto = "Atualização no Site - {$dadosNotificacao['categoria']}";
            $mensagem = view('Email.notificacao-peca', ['dadosNotificacao' => $dadosNotificacao, 'pessoa' => $pessoa])->render();
            $retorno = $this->mitteProService->enviarEmail($pessoa->nome_pessoa, $pessoa->email_pessoa, $assunto, $mensagem);

            // if (!$retorno) {
            //     throw new Exception('Erro ao enviar notificação por e-mail.');
            // }

            return $retorno;
        } catch (Exception $e) {
            Log::error('Erro ao enviar notificação por e-mail: ' . $e->getMessage());

            return false;
        }
    }

    // private function enviarNotificacaoWhatsApp($pessoa, $dadosNotificacao)
    // {
    // }

    // private function enviarNotificacaoSms($pessoa, $dadosNotificacao)
    // {
    // }
}
