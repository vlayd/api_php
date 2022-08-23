<?php

namespace Repository;

use DB\MySQLAutos;

class AutosRepository {
    private object $MySQLAutos;
    const TABELA = 'autos';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct() {
        $this->MySQLAutos = new MySQLAutos();
    }

    /**
     * @param $login
     * @param $senha
     * @return int
     */
    public function insertAuto($dados) {
        $consultaInsert = 'INSERT INTO ' . self::TABELA .
            ' (tipo, data_doc, assinatura1, assinatura2, ass_fornecedor, data_recebimento, fato, hora, lista_doc, prazo_adeq, prazo_doc)
         VALUES
              (:tipo,:data_doc,:assinatura1,:assinatura2,:ass_fornecedor,:data_recebimento,:fato,:hora,:lista_doc,:prazo_adeq,:prazo_doc)';
        $this->MySQLAutos->getDb()->beginTransaction();
        $stmt = $this->MySQLAutos->getDb()->prepare($consultaInsert);//Evitar script por requisição
        $stmt->bindParam(':tipo', $dados['tipo']);
        $stmt->bindParam(':data_doc', $dados['data_doc']);
        $stmt->bindParam(':assinatura1', $dados['assinatura1']);
        $stmt->bindParam(':assinatura2', $dados['assinatura2']);
        $stmt->bindParam(':ass_fornecedor', $dados['ass_fornecedor']);
        $stmt->bindParam(':data_recebimento', $dados['data_recebimento']);
        $stmt->bindParam(':fato', $dados['fato']);
        $stmt->bindParam(':hora', $dados['hora']);
        $stmt->bindParam(':lista_doc', $dados['lista_doc']);
        $stmt->bindParam(':prazo_adeq', $dados['prazo_adeq']);
        $stmt->bindParam(':prazo_doc', $dados['prazo_doc']);
        $stmt->execute();
        return $stmt->rowCount(); //Quantas linhas foram afetadas
    }

    /**
     * @return MySQLAutos|object
     */
    public function getMySQLAutos() {
        return $this->MySQLAutos;
    }
}