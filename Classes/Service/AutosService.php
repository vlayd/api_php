<?php

namespace Service;

use InvalidArgumentException;
use Repository\AutosRepository;
use Util\ConstantesGenericasUtil;

class AutosService {
    public const TABELA = 'autos';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_POST = ['cadastrar'];

    private array $dados;
    private array $dadosCorpoRequest;

    private object $AutosRepository;

    /**
     * AutosService constructor.
     * @param array $dados
     */
    public function __construct($dados = []) {
        //A request recebida por esse construtor é atribuida a variável global $this->dados
        //(18) Se o metodo for get, (VOLTAR para o método get() do RequestValidator)
        $this->dados = $dados;
        $this->AutosRepository = new AutosRepository();
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
            $retorno = $this->dados['id'] > 0
                ? $this->getOneByKey() : $this->$recurso(); //Equivale $this->listar()
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
        return $this->AutosRepository->getMySQLAutos()->getAll(self::TABELA);
    }

    /**
     * @return mixed
     */
    private function getOneByKey() {
        return $this->AutosRepository->getMySQLAutos()->getOneByKey(self::TABELA, $this->dados['id']);

    }

    /**
     * @return array
     */
    private function cadastrar() {
        //get valores pasados de RequestValidator pelo setDadosCorpoRequest() dessa classe
        //Verifica se fez a inserção
        if ($this->AutosRepository->insertAuto($this->dadosCorpoRequest) > 0) {
            $idInserido = $this->AutosRepository->getMySQLAutos()->getDb()->lastInsertId();
            $this->AutosRepository->getMySQLAutos()->getDb()->commit();
            return ['id_inserido' => $idInserido];
        }

        $this->AutosRepository->getMySQLAutos()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

    }
}