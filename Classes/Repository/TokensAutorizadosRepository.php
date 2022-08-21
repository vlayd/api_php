<?php

namespace Repository;

use DB\MySQL;
use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class TokensAutorizadosRepository {
    private object $MySQL;
    private object $UsuariosRepository;
    public const TABELA = 'tokens_autorizados';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQL = new MySQL();
        $this->UsuariosRepository = new UsuariosRepository();
    }

    /**
     * @param $token
     */
    public function validarToken($token) {
        //é retirado o desnecessário pra deixar o tokem limpo
        $token = str_replace([' ', 'Bearer'], '', $token);
        //(14) O token vai testado se não é vazio
        if ($token) {
            //Se o token não for vazio, vai ser testado se há no bd e se está ativo
            $consultaToken = 'SELECT id FROM ' . self::TABELA . ' WHERE token = :token AND status = :status';
            $stmt = $this->getMySQL()->getDb()->prepare($consultaToken);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':status', ConstantesGenericasUtil::SIM);
            $stmt->execute();
            if ($stmt->rowCount() !== 1) {
                header("HTTP/1.1 401 Unauthorized");
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO);
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_VAZIO);
        }
    }

    /**
     * @param $login
     * @param $senha
     */
    public function validarLoginSenha($login, $senha) {
        if ($login && $senha) {
            //Se o token não for vazio, vai ser testado se há no bd e se está ativo
            $consultaLoginSenha = $this->UsuariosRepository->login($login, $senha);
            if ($consultaLoginSenha !== 1) {
                header("HTTP/1.1 401 Unauthorized");
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_ERRADA);
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_VAZIO);
        }
    }

    /**
     * @return MySQL|object
     */
    public function getMySQL()
    {
        return $this->MySQL;
    }
}