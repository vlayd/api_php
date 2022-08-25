<?php

namespace Util;

class RotasUtil {
    /**
     * @return array
     */
    public static function getRotas(){
        //(2) $url pega as ROTAS e manda getUrls() para desmembrar
        $urls = self::getUrls();

        //4 com o array $urls formado pelo elementos soltos da rota adicionar ao array $request
        //cada parte recebe uma chave nomeada
        //Elemento 1: rota (nome da tabela) e põe tudo em maiúculo
        //Elemento 2: recurso (listar, logar, cadastrar, atualizar e deletar)
        //Elemento 3: id (se houver, só para listar, atualizar e deletar)
        //O tipo de método é reconhecido pelo $_SERVER['REQUEST_METHOD'] : metodo (GET, POST, PUT, DELETE)
        $request = [];
        $request['rota'] = strtoupper($urls[0]);
        $request['recurso'] = $urls[1] ?? null;
        $request['id'] = $urls[2] ?? null;
//        $request['id'] = filter_var($urls[2], FILTER_SANITIZE_NUMBER_INT) ?? null; //Ele só aceita id (int)
        $request['metodo'] = $_SERVER['REQUEST_METHOD'];

        //é retornado para o index.php
        return $request;
    }

    /**
     * @return false|string[]
     */
    public static function getUrls(){
        //(3) pega a ROTA primeiro tira a / dela ($_SERVER['REQUEST_URI']) e retorna uma array pelo elemento soltos
        $uri = str_replace('/' . DIR_PROJETO, '', $_SERVER['REQUEST_URI']);
        //O trim dá mais uma força tirando os espaços e / restantes
        return explode('/', trim($uri, '/'));
    }
}