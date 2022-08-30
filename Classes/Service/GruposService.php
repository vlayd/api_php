<?php

namespace Service;

use InvalidArgumentException;
use Repository\GruposRepository;
use Util\ConstantesGenericasUtil;

class GruposService {
    public const TABELA = 'grupos';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_DELETE = ['deletar'];

    private array $dados;
    private array $dadosCorpoRequest;
    /**
     * @var object|GruposRepository
     */
    private object $GruposRepository;

    /**
     * GruposService constructor.
     * @param array $dados
     */
    public function __construct($dados = []) {
        $this->dados = $dados;
        $this->GruposRepository = new GruposRepository();
    }

    /**
     * @return mixed
     */
    public function validarGet() {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {
            $retorno = $this->dados['id'] > 0
                ? $this->getOneByKey($this->dados['recurso']) : $this->$recurso(); //Equivale $this->listar()
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    /**
     * @return mixed
     */
    public function validarDelete() {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_DELETE, true)) {
            if ($this->dados['id'] > 0) {
                $retorno = $this->$recurso();
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    /**
     * @return mixed
     */
    public function validarPost() {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->$recurso();
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
        return $retorno;
    }



    /**
     * @param array $dadosCorpoRequest
     */
    public function setDadosCorpoRequest($dadosCorpoRequest) {
        $this->dadosCorpoRequest = $dadosCorpoRequest;
    }

    /**
     * @return mixed
     */
    private function listar() {
        return $this->GruposRepository->getMySQLGrupos()->getAll(self::TABELA);
    }

    /**
     * @return mixed
     */
    private function getOneByKey($recurso) {
        if($recurso == 'listar'){
            return $this->GruposRepository->getMySQLGrupos()->getOneByKey(self::TABELA, $this->dados['id']);
        } elseif ($recurso == 'logar'){
            return $this->GruposRepository->getMySQLGrupos()->getOneByLoginSenha(self::TABELA,
                getallheaders()['login'], getallheaders()['senha']);
        }
    }

    /**
     * @return array
     */
    private function cadastrar() {
        if ($this->GruposRepository->getRegistroByGrupo($this->dadosCorpoRequest['nome']) > 0) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GRUPO_EXISTENTE);
        }
        if ($this->GruposRepository->insertGrupo($this->dadosCorpoRequest) > 0) {
            $idInserido = $this->GruposRepository->getMySQLGrupos()->getDb()->lastInsertId();
            $this->GruposRepository->getMySQLGrupos()->getDb()->commit();
            return ['id_inserido' => $idInserido];
        }
        $this->GruposRepository->getMySQLGrupos()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /**
     * @return string
     */
    private function deletar()
    {
        return $this->GruposRepository->getMySQLGrupos()->delete(self::TABELA, $this->dados['id']);
    }

}