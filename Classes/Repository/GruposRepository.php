<?php

namespace Repository;

use DB\MySQLGrupos;

class GruposRepository {
    private object $MySQLGrupos;
    const TABELA = 'grupos';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQLGrupos = new MySQLGrupos();
    }


    /**
     * @param $grupo
     * @return int
     */
    public function getRegistroByGrupo($grupo){
        $consulta = 'SELECT DISTINCT nome FROM ' . self::TABELA . ' WHERE UPPER(nome) LIKE UPPER("%":nome"%")';
        $stmt = $this->MySQLGrupos->getDb()->prepare($consulta);
        $stmt->bindParam(':nome', $grupo);
        $stmt->execute();
        return $stmt->rowCount();
    }
    /**
     * @param $dados
     * @return int
     */
    public function insertGrupo($dados) {
        $consultaInsert = 'INSERT INTO ' . self::TABELA . ' (nome, docs) VALUES (:nome, :docs)';
        $this->MySQLGrupos->getDb()->beginTransaction();
        $stmt = $this->MySQLGrupos->getDb()->prepare($consultaInsert);//Evitar script por requisição
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':docs', $dados['docs']);
        $stmt->execute();
        return $stmt->rowCount(); //Quantas linhas foram afetadas
    }

    /**
     * @return MySQLGrupos|object
     */
    public function getMySQLGrupos() {
        return $this->MySQLGrupos;
    }
}