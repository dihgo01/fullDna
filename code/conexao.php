<?php
header('Access-Control-Allow-Origin: *');
class Connection extends PDO { 
    /*
    private $database = 'mysql:hostfull-dna.cuazinkxlwra.us-east-2.rds.amazonaws.com; dbname=parrotsolucoes03; charset=utf8;';
    private $user = 'parrotsolucoes03';
    private $password = 'mindercopy2021';
    public static $handle = null; 

    private $database = 'mysql:host=localhost; dbname=full_dna; charset=utf8;';
    private $user = 'root';
    private $password = '';
    public static $handle = null;*/

    private $database = 'mysql:host=fulldna-1.cuazinkxlwra.us-east-2.rds.amazonaws.com; dbname=full_dna; charset=utf8;';
    private $user = 'boybimbi';
    private $password = 'boybimbi123';
    public static $handle = null;

    function __construct() {
        try {
            if (self::$handle == null) {
                $connection_data = parent::__construct($this->database, $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
                self::$handle = $connection_data;
                return self::$handle;
            }
        } catch (PDOException $e) {
            echo 'Falha na conexão: ' . $e->getMessage();
            return false;
        }
    }

    function __destruct() {
        self::$handle = NULL;
    }

}

?>