<?php

namespace Service;

use InvalidArgumentException;
use Repository\UsuariosRepository;
use Util\ConstantesGenericasUtil;

class UsuariosService {
    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados;
    private array $dadosCorpoRequest;
    /**
     * @var object|UsuariosRepository
     */
    private object $UsuariosRepository;

    /**
     * UsuariosService constructor.
     * @param array $dados
     */
    public function __construct($dados = []) {
        //A request recebida por esse construtor é atribuida a variável global $this->dados
        //(18) Se o metodo for get, (VOLTAR para o método get() do RequestValidator)
        $this->dados = $dados;
        //TODO Inicializou o UsuariosRepository não sei porque
        $this->UsuariosRepository = new UsuariosRepository();
    }

    /**
     * @return mixed
     */
    public function validarGet() {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        //(20) Vê se o dados['recurso'] consta entro os elementos de RECURSOS_GET (listar)
        if (in_array($recurso, self::RECURSOS_GET, true)) {
            //Se consta, agora verifica se vem acompanha do id
            //Se sim vai para o método $this->getOneByKey(), se não vai para método do seu próprio nome listar()
            $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso(); //Equivale $this->listar()
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
        //(22) Como a variável dados já tem a request toda, pega só o ['recurso'] que é cadastar
        //O retorno é nulo como padrão, mas pode mudar
        $retorno = null;
        $recurso = $this->dados['recurso'];
        //confere se o cadastrar está dentro do RECURSOS_POST
        if (in_array($recurso, self::RECURSOS_POST, true)) {
            //O retorno deixa de ser nulo e chama o método com o mesmo nome cadastrar()
            $retorno = $this->$recurso();
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
        //Se chegou aqui pq o recurso não é nulo e vai ser devolvido para o post() de RequestValidator
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
        //(20) O $dadosCorpoRequest que vem do post() ou do put() do RequestValidator, com os valores para add ou edit
        //Retorna a alguns desse métodos com a variável global $dadosCorpoRequest já atribuida com os dados recebidos
        $this->dadosCorpoRequest = $dadosCorpoRequest;
    }

    /**
     * @return mixed
     */
    private function listar() {
        //(22) Pelo método getMySQL() - que representa a classe MySQL, vamos chamar a método getAll() passando a tabela
        return $this->UsuariosRepository->getMySQL()->getAll(self::TABELA);
    }

    /**
     * @return mixed
     */
    private function getOneByKey() {
        return $this->UsuariosRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    /**
     * @return array
     */
    private function cadastrar() {
        //Forma mais fácil de criar varáveis
        [$login, $senha] = [$this->dadosCorpoRequest['login'], $this->dadosCorpoRequest['senha']];

        if ($login && $senha) {
            //Verifica se já existe o login
            if ($this->UsuariosRepository->getRegistroByLogin($login) > 0) {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_EXISTENTE);
            }

            //Verifica se fez a inserção
            if ($this->UsuariosRepository->insertUser($login, $senha) > 0) {
                $idInserido = $this->UsuariosRepository->getMySQL()->getDb()->lastInsertId();
                $this->UsuariosRepository->getMySQL()->getDb()->commit();
                return ['id_inserido' => $idInserido];
            }

            $this->UsuariosRepository->getMySQL()->getDb()->rollBack();

            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
    }

    /**
     * @return string
     */
    private function deletar()
    {
        return $this->UsuariosRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /**
     * @return string
     */
    private function atualizar(){
        if ($this->UsuariosRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
            $this->UsuariosRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }
        $this->UsuariosRepository->getMySQL()->getDb()->rollBack();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

}