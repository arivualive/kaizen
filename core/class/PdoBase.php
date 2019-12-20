<?php

#require_once 'config.php';
#require_once 'PdoBase.php';

class PdoBase
{
    private static $config;
    protected $db;

    public function __construct()
    {
        $this->initDb();
    }

    public static function setConnectionInfo($config)
    {
        self::$config = $config;
    }

    public function initDb()
    {
        try{
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8;',
                self::$config['host'],
                self::$config['dbname']
            );

            $this->db = new PDO($dsn, self::$config['dbuser'], self::$config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            header('Content-Type: text/plain; charset=UTF-8', true, 500);
            exit($e->getMessage());
            #echo "Connection failed: " . $e->getMessage();
        }
    }

    public function exec($sql, $params = array())
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function execute($sql, $params = array())
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch($sql, $params = array()) {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
   }
}
