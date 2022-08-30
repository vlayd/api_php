<?php

namespace Validator;

use InvalidArgumentException;
use Repository\TokensAutorizadosRepository;
use Service\AutosService;
use Service\DocumentosService;
use Service\GruposService;
use Service\UsuariosService;
use Util\ConstantesGenericasUtil;
use Util\JsonUtil;

class RequestValidator {
    private array $request;
    private array $dadosRequest;
    private object $TokensAutorizadosRepository;

    const GET = 'GET';
    const DELETE = 'DELETE';
    const USUARIOS = 'USUARIOS';
    const AUTOS = 'AUTOS';
    const DOCUMENTOS = 'DOCUMENTOS';
    const GRUPOS = 'GRUPOS';

    /**
     * RequestValidator constructor.
     * @param array $request
     */
    public function __construct($request = []) {
        //(7) recebe a rota como array já nomeadas
        $this->TokensAutorizadosRepository = new TokensAutorizadosRepository(); //TODO token deve ser testado antes
        //(8) atribui a rota recebida a variável global $request (VOLTAR P/ INDEX)
        $this->request = $request;
    }

    /**
     * @return array|mixed|string|null
     */
    public function processarRequest() {
        //Mensagem de erro já é padrão, a não ser que mude
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        //(10) verifica se o request['metodo'] contém dentro do array da const TIPO_REQUEST[get,post,delete,put]
        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true)) {
            //Se ele contém dentro das constante, o $retorno deixa de ser erro e é chamado o método direcionarRequest()
            $retorno = $this->direcionarRequest();
        }
        return $retorno;
    }

    /**
     * Metodo para direcionar o tipo de Request
     * @return array|mixed|string|null
     */
    private function direcionarRequest(){
        //(11) método chamado vê que tipo de request['metodo'] está vindo NÃO é GET OU DELETE
        if ($this->request['metodo'] !== self::GET && $this->request['metodo'] !== self::DELETE) {
            //Se NÃO for um desses método é chamado o tratarCorpoRequisicaoJson() e atribuido a variável global dadosRequest
            $this->dadosRequest = JsonUtil::tratarCorpoRequisicaoJson();
        }
        //(13) //var_dump(getallheaders()); descobre a array['Authorization'], que reconhece o token que manda para validar
        $this->TokensAutorizadosRepository->validarLoginSenha(getallheaders()['login'], getallheaders()['senha']);
        //(15) pelo tipo do request['metodo'] vai ser chamado pelo nome dele (post(), get(), delete(), pub())
        $metodo = $this->request['metodo'];
        //Primeiro vai ser o get()
        //Segundo vai ser o post()
        return $this->$metodo();
    }

    /**
     * Metodo para tratar os DELETES
     * @return mixed|string
     */
    private function delete(){
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_DELETE, true)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuariosService($this->request);
                    $retorno = $UsuariosService->validarDelete();
                    break;
                case self::DOCUMENTOS:
                    $DocumentosService = new DocumentosService($this->request);
                    $retorno = $DocumentosService->validarDelete();
                    break;
                case self::GRUPOS:
                    $GruposService = new GruposService($this->request);
                    $retorno = $GruposService->validarDelete();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    /**
     * Metodo para tratar os GETS
     * @return array|mixed|string
     */
    private function get(){
        //(16) Verifica se request['rota'] está nos db permitido dentro do TIPO_GET
        //Já inicia o $retorno com erro de erro, que pode mudar
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET, true)) {
            switch ($this->request['rota']) {
                //Se essa rota for igual ao da constante acima passa para a request da para a UsuarioService
                case self::USUARIOS:
                    $UsuariosService = new UsuariosService($this->request);
                    //(19) Agora o variável global $dados já recebeu $this->request da classe UsuariosService, para ela
                    // pelo método validarGet
                    $retorno = $UsuariosService->validarGet();
                    break;
                case self::AUTOS:
                    $AutosService = new AutosService($this->request);
                    $retorno = $AutosService->validarGet();
                    break;
                case self::DOCUMENTOS:
                    $DocumentosService = new DocumentosService($this->request);
                    $retorno = $DocumentosService->validarGet();
                    break;
                case self::GRUPOS:
                    $GruposService = new GruposService($this->request);
                    $retorno = $GruposService->validarGet();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $retorno;
    }

    /**
     * Metodo para tratar os POSTS
     * @return array|null|string
     */
    private function post(){
        //(16) Confere se o db bate request['rota']
        $retorno = null;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST, true)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    //Pega a request toda e passa pelo construtor de UsuariosService()
                    $UsuariosService = new UsuariosService($this->request);
                    //(19) O $dado global já tá com ela atribuida
                    //Instancia o método setDadosCorpoRequest() passando o $this->dadosRequest pego do direcionarRequest
                    $UsuariosService->setDadosCorpoRequest($this->dadosRequest);
                    //(21) É só voltar para a classe $UsuariosService, pois a variável global $dados dela já tem o valor
                    $retorno = $UsuariosService->validarPost();
                    break;
                case self::AUTOS:
                    $AutosService = new AutosService($this->request);
                    $AutosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $AutosService->validarPost();
                    break;
                case self::DOCUMENTOS:
                    $DocumentosService = new DocumentosService($this->request);
                    $DocumentosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $DocumentosService->validarPost();
                    break;
                case self::GRUPOS:
                    $GruposService = new GruposService($this->request);
                    $GruposService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $GruposService->validarPost();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
            }
            //Agora o $retorno não é nulo e vai ser devolvido para o direcionarRequest acima
            return $retorno;
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
    }

    /**
     * Metodo para tratar os PUTS
     * @return array|null|string
     */
    private function put(){
        $retorno = null;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_PUT, true)) {
            switch ($this->request['rota']) {
                case self::USUARIOS:
                    $UsuariosService = new UsuariosService($this->request);
                    $UsuariosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $UsuariosService->validarPut();
                    break;
                case self::DOCUMENTOS:
                    $DocumentosService = new DocumentosService($this->request);
                    $DocumentosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $DocumentosService->validarPut();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
            }
            return $retorno;
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
    }
}