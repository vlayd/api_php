<?php

use Util\ConstantesGenericasUtil;
use Util\JsonUtil;
use Util\RotasUtil;
use Validator\RequestValidator;

include 'bootstrap.php';

try {
    //(1) pega as rotas (url + ROTAS) e manda RotasUtil::getRotas() para desmembrar
    //(6) O RotasUtil::getRotas() é retornado em forma de array e posto dentro do RequestValidator() para ser validado
    $RequestValidator = new RequestValidator(RotasUtil::getRotas());
    //(9) chama o método processarRequest() com o variável global $request da classe dela já atribuido
    $retorno = $RequestValidator->processarRequest();

    $JsonUtil = new JsonUtil;
    $JsonUtil->processarArrayParaRetornar($retorno);

} catch (Exception $e){
    echo json_encode([
        ConstantesGenericasUtil::TIPO=>ConstantesGenericasUtil::TIPO_ERRO,
        ConstantesGenericasUtil::RESPOSTA=>$e->getMessage()
    ]);
    exit;
}