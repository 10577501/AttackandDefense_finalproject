<?php

/**
 * @author weiwenping
 *
 */
class DBMySQL
{
    
    protected static $db = null;
    protected $params = array();
    
    public function __construct()
    {
        isset(self::$db) || self::_connect();

    }
    
    private function __clone(){}
    
    private static function _connect() {
        $config = require_once SYSTEM_PATH.'data/config.php';
        $dsn = "{$config['db']}:host={$config['host']};port={$config['port']};
        dbname={$config['dbname']};charset={$config['charset']}";
        
        try {
            self::$db = new PDO($dsn, $config['user'], $config['psd']);
        } catch (PDOException $e) {
            if(APP_DEBUG){
                wmerror('数据库连接失败：' . $e->getMessage());
            }else{
                wmerror('数据库连接失败!');
            }
        }
    }
        /**
     * 通过预处理方式执行SQL
     * @param string $sql 执行的SQL语句模板
     * @param array $data 数据 
     * @return object mysqli_stmt
     */
    public function query($sql, $batch  = []) {

        $data = $batch ? $this->params: array($this->params);
        $this->params= array();
        $stmt = self::$db->prepare($sql);
        foreach ($data as $v) {
            if ($stmt->execute($v) === false) {
                exit('数据库操作失败：'. implode('-', $stmt->errorInfo()));
            }
        }
        return $stmt;
    }
    
    public function params($params) {
        $this->params= $params;
        return $this;
    }
    
    public function fetchRow($sql) {
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchAll($sql) {
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchColumn($sql) {
        return $this->query($sql)->fetchColumn();
    }
    public function lastInsertId() {
        return self::$db->lastInsertId();
    }

}

