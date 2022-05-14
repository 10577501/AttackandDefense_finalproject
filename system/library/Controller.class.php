<?php

class Controller
{
    protected $user = array();
    protected $model = NULL;
    
    public function __construct(){
        isset($_SESSION ) || session_start();
        if (isset($_SESSION['user'])) {
            define(ISLOGIN, TRUE);
            $this->user = $_SESSION['user'];
        }else{
            define(ISLOGIN, FALSE);
        }
    }
    public function __call($name, $args) {
        wmerror('您访问的操作不存在！');        
    }
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
    protected function success($msg='', $target='' ) {
        $this->returnData(array('ok'=>true,'msg'=>$msg,'target'=>$target));
    }
    protected function error($msg='', $target='' ) {
        $this->returnData(array('ok'=>false,'msg'=>$msg,'target'=>$target));
    }
    protected function returnData($data) {
        return $data;
    }    
}

