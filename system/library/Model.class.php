<?php

class Model extends DBMySQL
{
    protected $table = '';    
    protected $error = array();
    
    public function __construct($table=FALSE){
        parent::__construct();
        $this->table = $table ? $table : '';
    }
    //获取全部数据
    public function getData(){
        $data = array();
        $sql = 'select * from '.$this->table;
        $data = $this->fetchAll($sql);
        return $data;        
    }

    //根据ID获取数据
    public function getDataById($id) {
        $data = array();
        $sql = 'select * from '.$this->table.' where id = :id';
        $data = $this->params(array('id'=>$id))->fetchRow($sql);
        return $data;
    }
    
    public function __get($name) {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
    public function getError() {
        return $this->error;
    }
}

