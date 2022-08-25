<?php

namespace Repository;

use DB\MySQLDocumentos;

class DocumentosRepository {
    private object $MySQLDocumentos;
    const TABELA = 'documentos';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQLDocumentos = new MySQLDocumentos();
    }

    /**
     * @param $login
     * @return mixed
     */
    public function getRegistroByDoc($doc){
        $consulta = 'SELECT DISTINCT nome FROM ' . self::TABELA . ' WHERE UPPER(nome) LIKE UPPER("%":nome"%")';
        $stmt = $this->MySQLDocumentos->getDb()->prepare($consulta);
        $stmt->bindParam(':nome', $doc);
        $stmt->execute();
        return $stmt->rowCount();
    }
    /**
     * @param $dados
     * @return int
     */
    public function insertDoc($dados) {
        $consultaInsert = 'INSERT INTO ' . self::TABELA . ' (nome) VALUES (:nome)';
        $this->MySQLDocumentos->getDb()->beginTransaction();
        $stmt = $this->MySQLDocumentos->getDb()->prepare($consultaInsert);//Evitar script por requisição
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->execute();
        return $stmt->rowCount(); //Quantas linhas foram afetadas
    }

    /**
     * @param $id
     * @param $login
     * @param $senha
     * @return int
     */
    public function updateDoc($id, $dados) {
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET nome = :nome WHERE id = :id';
        $this->MySQLDocumentos->getDb()->beginTransaction();
        $stmt = $this->MySQLDocumentos->getDb()->prepare($consultaUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @return MySQLDocumentos|object
     */
    public function getMySQLDocumentos() {
        return $this->MySQLDocumentos;
    }
}