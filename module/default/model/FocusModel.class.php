<?php

/** 
 * @author weiwenping
 * 
 */
class FocusModel extends Model
{
    public function __construct(){
        parent::__construct('test_focus');
    }
    //更新浏览次数
    public function vists($id) {
        $row = $this->getDataById($id);
        $v = $row['visits'] + 1;
        $sql = 'update '.$this->table.' set visits = :v where id = :id';
        $this->params(array('v'=>$v,'id'=>$id))->query($sql);
    }
}

