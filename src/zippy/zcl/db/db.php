<?php

namespace ZCL\DB;

/**
 * Синглетон  инкапсулирующий   конект  к  БД
 * @todo  транзакции
 */
class DB
{

    private $conn = null;
    private static $db = null, $driver = "mysqli";
    private static $dbhost, $dbname, $dbuser, $dbpassword;

    private function __construct()
    {
        
    }

    /**
     * Установка  параметров   коннекта
     *   
     * @param mixed $dbhost
     * @param mixed $dbname
     * @param mixed $dbuser
     * @param mixed $dbpassword
     */
    public static function config($dbhost, $dbname, $dbuser, $dbpassword)
    {
        self::$dbhost = $dbhost;
        self::$dbname = $dbname;
        self::$dbuser = $dbuser;
        self::$dbpassword = $dbpassword;
    }

    /**
     * Возвращает  инстанс 
     * 
     */
    public static function getDB()
    {
        if (self::$db == null) {
            self::$db = new DB();
        }
        return self::$db;
    }

    /**
     * Открывает  конект  к  БД  и  возвращает  соотаветствующий  ресурс
     * 
     */
    public static function getConnect()
    {
        $db = DB::getDB();
        $db->open();
        //$db->conn->debug = true;
        return $db->conn;
    }

    /**
     * Закрывает  конект  к  БД
     * 
     */
    public static function Close()
    {
        $db = DB::getDB();
        if ($db->conn instanceof \ADOConnection) {
            $db->conn->Close();
        }
    }

    private function open()
    {
        if ($this->conn instanceof \ADOConnection) {
            return;
        }
        $this->conn = \ADONewConnection(self::$driver);
        $this->conn->Connect(self::$dbhost, self::$dbuser, self::$dbpassword, self::$dbname);
        $this->conn->Execute("SET NAMES 'utf8'");
    }

}
