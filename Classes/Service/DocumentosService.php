<?php

namespace Service;

use InvalidArgumentException;
use Repository\DocumentosRepository;
use Util\ConstantesGenericasUtil;

class DocumentosService {
    public const TABELA = 'documentos';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados;
    private array $dadosCorpoRequest;
    /**
     * @var object|DocumentosRepository
     */
    private object $DocumentosRepository;

    /**
     * DocumentosService constructor.
     * @param array $dados
     */
    public function __construct($dados = []) {
        $this->dados = $dados;
        $this->DocumentosRepository = new DocumentosRepository();
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
     * @return mixed
     */
    public function validarPut() {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_PUT, true)) {
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
     * @param array $dadosCorpoRequest
     */
    public function setDadosCorpoRequest($dadosCorpoRequest) {
        $this->dadosCorpoRequest = $dadosCorpoRequest;
    }

    /**
     * @return mixed
     */
    private function listar() {
        return $this->DocumentosRepository->getMySQLDocumentos()->getAll(self::TABELA);
    }

    /**
     * @return mixed
     */
    private function getOneByKey($recurso) {
        if($recurso == 'listar'){
            return $this->DocumentosRepository->getMySQLDocumentos()->getOneByKey(self::TABELA, $this->dados['id']);
        } elseif ($recurso == 'logar'){
            return $this->DocumentosRepository->getMySQLDocumentos()->getOneByLoginSenha(self::TABELA,
                getallheaders()['login'], getallheaders()['senha']);
        }
    }

    /**
     * @return array
     */
    private function cadastrar() {
        if ($this->DocumentosRepository->getRegistroByDoc($this->dadosCorpoRequest['nome']) > 0) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_DOC_EXISTENTE);
        }
        if ($this->DocumentosRepository->insertDoc($this->dadosCorpoRequest) > 0) {
            $idInserido = $this->DocumentosRepository->getMySQLDocumentos()->getDb()->lastInsertId();
            $this->DocumentosRepository->getMySQLDocumentos()->getDb()->commit();
            return ['id_inserido' => $idInserido];
        }
        $this->DocumentosRepository->getMySQLDocumentos()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /**
     * @return string
     */
    private function deletar()
    {
        return $this->DocumentosRepository->getMySQLDocumentos()->delete(self::TABELA, $this->dados['id']);
    }

    /**
     * @return string
     */
    private function atualizar(){
        if ($this->DocumentosRepository->updateDoc($this->dados['id'], $this->dadosCorpoRequest) > 0) {
            $this->DocumentosRepository->getMySQLDocumentos()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }
        $this->DocumentosRepository->getMySQLDocumentos()->getDb()->rollBack();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

}