<?php

/** 
 * @author weiwenping
 * 
 */
class UserModel extends Model
{
    public function __construct(){
        parent::__construct('test_user');
    }
        /**
     * 添加用户
     * @param array $data
     * @return int $id 新记录ID 添加成功
     * @return NULL 添加失败
     */
    public function addUser($data) {
        $sql = 'insert into '.$this->table.'(id,name,password,phone) values(null,"'.$data['username'].'","'.$data['password'].'","'.$data['phone'].'")';
        $stmt = $this->query($sql);
        $id = $stmt -> insert_id;
        if ($id === NULL) {
            //die($dbh->errorInfo());
            $this->errors['insert'] = $stmt -> error;
        }
        return $id;
    }


}

