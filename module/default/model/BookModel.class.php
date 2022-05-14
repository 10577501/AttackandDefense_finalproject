<?php

/** 
 * @author weiwenping
 * 
 */
class BookModel extends Model
{
    public function __construct(){
        parent::__construct();
        $this->data = $this->data['book'];
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getDataById($id) {
        $data = array();
        foreach ($this->data as $key => $v){
            if ($key == 'book' && $v['id'] == $id) {
                $data = $v;
                break;
            }
        }
        return $data;
    }
}

