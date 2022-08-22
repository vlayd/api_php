<?php

namespace DB;

use InvalidArgumentException;
use PDO;
use PDOException;
use Util\ConstantesGenericasUtil;

class MySQL {
    private object $db;

    /**
     * MySQL constructor.
     */
    public function __construct()
    {
        $this->db = $this->setDB();
    }

    /**
     * @return PDO
     */
    public function setDB()
    {
        try {
            return new PDO(
                'mysql:host=' . HOST . '; dbname=' . BANCO . ';', USUARIO, SENHA
            );
        } catch (PDOException $exception) {
            throw new PDOException($exception->getMessage());
        }
    }

    /**
     * @param $tabela
     * @param $id
     * @return string
     */
    public function delete($tabela, $id)
    {
        $consultaDelete = 'DELETE FROM ' . $tabela . ' WHERE id = :id';
        if ($tabela && $id) {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($consultaDelete);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->db->commit();
                return ConstantesGenericasUtil::MSG_DELETADO_SUCESSO;
            }
            $this->db->rollBack();
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /**
     * @param $tabela
     * @return array
     */
    public function getAll($tabela){
        //(23)Se há a tabela tabela, vamos retor fazer a pesquisa
        if ($tabela) {
            $consulta = 'SELECT * FROM ' . $tabela;
            $stmt = $this->db->query($consulta);
            $registros = $stmt->fetchAll($this->db::FETCH_ASSOC);
            //Se $registro for array e tiver registro retorne esse resultados para o método Lista() de UsuárioService
            if (is_array($registros) && count($registros) > 0) {
                return $registros; //todo fim do processo
            }
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /**
     * @param $tabela
     * @param $id
     * @return mixed
     */
    public function getOneByKey($tabela, $id) {
        if ($tabela && $id) {
            $consulta = 'SELECT * FROM ' . $tabela . ' WHERE id = :id';
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $totalRegistros = $stmt->rowCount();
            if ($totalRegistros === 1) {
                return $stmt->fetch($this->db::FETCH_ASSOC);
            }
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
    }

    /**
     * @param $tabela
     * @param $login
     * @param $senha
     * @return mixed
     */
    public function getOneByLoginSenha($tabela, $login, $senha) {
        if ($tabela && $login && $senha) {
            $consulta = 'SELECT * FROM ' . $tabela . ' WHERE login = :login AND senha = :senha';
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':senha', $senha);
            $stmt->execute();
            $totalRegistros = $stmt->rowCount();
            if ($totalRegistros === 1) {
                return $stmt->fetch($this->db::FETCH_ASSOC);
            }
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_VAZIO);
    }

    /**
     * @return object|PDO
     */
    public function getDb() {
        return $this->db;
    }
}

