<?php

namespace Util;

use InvalidArgumentException;
use JsonException;

class JsonUtil
{
    /**
     * @param $retorno
     * @throws JsonException
     */
    public function processarArrayParaRetornar($retorno) {
        $dados = [];
        $dados[ConstantesGenericasUtil::TIPO] = ConstantesGenericasUtil::TIPO_ERRO;

        if ((is_array($retorno) && count($retorno) > 0) || strlen($retorno) > 10) {
            $dados[ConstantesGenericasUtil::TIPO] = ConstantesGenericasUtil::TIPO_SUCESSO;
            $dados[ConstantesGenericasUtil::RESPOSTA] = $retorno;
        }

        $this->retornarJson($dados);
    }

    /**
     * @param $json
     * @throws JsonException
     */
    private function retornarJson($json) {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate'); //Não registrar em cache
        header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE'); //Controlar os tipos de requisições
        echo json_encode($json, JSON_THROW_ON_ERROR, 1024);
        exit;
    }

    /**
     * @return array|mixed
     */
    public static function tratarCorpoRequisicaoJson() {
        //(12) TODO ser tratado um json e transfoma-lo em array (procurar saber desse processo) (voltar p/ RequestValidator)
        try {
            $postJson = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERR0_JSON_VAZIO);
        }
        if (is_array($postJson) && count($postJson) > 0) {
            return $postJson;
        }
    }
}