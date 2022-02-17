<?php

if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');

date_default_timezone_set('Brazil/East');
include_once 'conexao.php';

class ClassesWeb
{

    private $pdo;

    /*
     * FUNCOES BASICAS DE CRUD
     * 1 - FUNÇAO DE UNICA QUERY COM APENAS UM REGISTRO DO BANCO DE DADOS
     * 2 - BUSCA TODAS EMPRESAS E TELEFONE NA TABELA EMPRESAS GRUPO CONTANTOS EMPRESARIAIS 
     * 3 - FUNÇAO DE INSERIR DADOS NO BANCO DE DADOS
     * 4 - FUNÇAO DE ATUALIZAR DADOS NO BANCO DE DADOS
     * 5 - FUNÇAO DE DELETAR DADOS NO BANCO DE DADOS 
     * 6 - FUNÇAO DE DELETAR DADOS NO BANCO DE DADOS COM PERSONALIZAÇAO DO WHERE
     * 7 - FUNÇAO DE BUSCA USUARIO PARA LOGIN 
     * 8 - FUNÇAO DE BUSCA USUARIO PELO HASH 
     * 9 - FUNÇÃO DE BUSCA AS EMPRESAS DO GRUPO QUE O USUÁRIO ATUA
     */

    /**
     *
     * [1] - FUNÇAO DE UNICA QUERY COM APENAS UM REGISTRO DO BANCO DE DADOS
     *
     */
    function get_query_unica($query)
    {
        $this->pdo = new Connection();
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    /**
     *
     * [2] - FUNÇAO DE UNICA QUERY COM TODOS OS REGISTROS DO BANCO DE DADOS
     *
     */
    function get_query($query)
    {
        $this->pdo = new Connection();
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':url', $pagina, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    /**
     *
     * [3] - FUNÇAO DE INSERIR DADOS NO BANCO DE DADOS
     *
     */
    function query_insert($campos, $parametros, $valores, $tabela)
    {
        $this->pdo = new Connection();
        $query = "INSERT INTO " . $tabela . " (" . $campos . ") VALUES (" . $parametros . ")";
        try {
            $stmt = $this->pdo->prepare($query, array("set names utf8"));
            $stmt->execute($valores);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * [4] - FUNÇAO DE ATUALIZAR DADOS NO BANCO DE DADOS
     *
     */
    function query_update($campos, $valores, $tabela, $where)
    {
        $this->pdo = new Connection();
        $query = "UPDATE " . $tabela . " SET " . $campos . " WHERE " . $where;
        try {
            $stmt = $this->pdo->prepare($query, array("set names utf8"));
            if ($stmt->execute($valores)) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * [5] - FUNÇAO DE DELETAR DADOS NO BANCO DE DADOS
     *
     */
    function query_delete($tabela, $id)
    {
        $this->pdo = new Connection();
        $query = "DELETE FROM " . $tabela . " WHERE id = " . $id;
        try {
            $stmt = $this->pdo->prepare($query, array("set names utf8"));
            if ($stmt->execute()) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * [6] - FUNÇAO DE DELETAR DADOS NO BANCO DE DADOS COM PERSONALIZAÇAO DO WHERE
     *
     */
    function query_delete_custom($tabela, $id)
    {
        $this->pdo = new Connection();
        $query = "DELETE FROM " . $tabela . " WHERE " . $id;
        try {
            $stmt = $this->pdo->prepare($query, array("set names utf8"));
            if ($stmt->execute()) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     * [7] - FUNÇAO DE BUSCA USUARIO PARA LOGIN 
     *
     */
    function fazer_login($login, $pass)
    {
        $this->pdo = new Connection();
        $query = "SELECT * FROM usuarios WHERE username = :login AND senha = :senha";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':senha', $pass, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    /**
     *
     * [8] - FUNÇAO DE BUSCA USUARIO PELO HASH 
     *
     */
    function busca_usuarios($hash = "")
    {
        if ($hash === "") {
            $this->pdo = new Connection();
            $query = "SELECT * FROM usuarios WHERE status = 'Ativo' ";
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $exc) {
                echo $exc->getMessage();
            }
            return $result;
        } else {
            $this->pdo = new Connection();
            $query = "SELECT * FROM usuarios WHERE hash = :hash AND status = 'Ativo' ";
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_OBJ);
            } catch (PDOException $exc) {
                echo $exc->getMessage();
            }
            return $result;
        }
    }

    /*     * *********************************************************************************** */

    /*
     *   FUNCOES DA TABELA MODULOS E FUNÇOES
     * 1 - BUSCA EMAIL NA TABELA DE USUARIOS 
     * 2 - BUSCA MÓDULO NA TABELA DE MÓDULOS  
     */

    /**
     * [1] - BUSCA EMAIL NA TABELA DE USUARIOS  
     */
    function consulta_email($email, $tabela)
    {
        $this->pdo = new Connection();
        $query = "SELECT * FROM $tabela WHERE email = :email";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    function consulta_login($login, $tabela)
    {
        $this->pdo = new Connection();
        $query = "SELECT * FROM $tabela WHERE username = :login";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }


    function busca_todos_arquivos($limit = '')
    {
        if ($limit === '') {
            $this->pdo = new Connection();
            $query = "SELECT *, U.nome as USUARIO, A.hash as HASH_FILE FROM arquivos A INNER JOIN usuarios U ON U.hash = A.hash_usuario WHERE A.status = 'Ativo'";
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $exc) {
                echo $exc->getMessage();
            }
            return $result;
        } else {
            $this->pdo = new Connection();
            $query = "SELECT *, U.nome as USUARIO, A.hash as HASH_FILE FROM arquivos A INNER JOIN usuarios U ON U.hash = A.hash_usuario WHERE A.status = 'Ativo' ORDER BY A.data_cadastro DESC LIMIT :limit";
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $exc) {
                echo $exc->getMessage();
            }
            return $result;
        }
    }
    function busca_quarto_arquivos_do_usuario($hash)
    {
        $this->pdo = new Connection();
        $query = "SELECT *, U.nome as USUARIO, A.hash as HASH_FILE FROM arquivos A INNER JOIN usuarios U ON U.hash = A.hash_usuario WHERE A.hash_usuario = :hash AND A.status = 'Ativo' LIMIT 4";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }


    function busca_todos_arquivos_do_usuario($hash)
    {
        $this->pdo = new Connection();
        $query = "SELECT *, U.nome as USUARIO, A.hash as HASH_FILE FROM arquivos A INNER JOIN usuarios U ON U.hash = A.hash_usuario WHERE A.hash_usuario = :hash AND A.status = 'Ativo'";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    function busca_todos_usuarios()
    {
        $this->pdo = new Connection();
        $query = "SELECT COUNT(*) as USER FROM usuarios WHERE status = 'Ativo';";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }


    function busca_todos_arquivos_salvos()
    {
        $this->pdo = new Connection();
        $query = "SELECT COUNT(*) as FILES FROM arquivos WHERE status = 'Ativo';";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    function busca_todos_arquivos_salvos_pelo_user($hash)
    {
        $this->pdo = new Connection();
        $query = "SELECT COUNT(*) as FILES FROM arquivos WHERE status = 'Ativo' AND hash = :hash;";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }

    function busca_total_de_downloads()
    {
        $this->pdo = new Connection();
        $query = "SELECT SUM(qtd_download) as DOWNLOADS FROM arquivos WHERE status = 'Ativo';";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }


    function busca_total_de_downloads_pelo_user($hash)
    {
        $this->pdo = new Connection();
        $query = "SELECT SUM(qtd_download) as DOWNLOADS FROM arquivos WHERE status = 'Ativo' AND hash = :hash;";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
        return $result;
    }
}
