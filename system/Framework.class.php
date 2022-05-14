<?php

class Framework
{
    //系统启动
    public static function run() {
        self::init();
        self::registerAutoLoad();
        self::dispatch();
    }
    //系统初始化
    private static function init() {
        //开启调试时，显示错误报告
        if (APP_DEBUG) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        }else{
            ini_set('display_errors', 0);
            error_reporting(0);
        }
        //定义项目常量
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', getcwd().DS);
        define('MODULES_PATH', ROOT.'module'.DS);
        define('SYSTEM_PATH', ROOT.'system'.DS);
        define('LIBRARY_PATH', SYSTEM_PATH.'library'.DS);
        define('COMMON_PATH', MODULES_PATH.'common'.DS);
        define('PUBLIC_PATH', './public'.DS);
        define('PCOMMON_PATH', PUBLIC_PATH.'common'.DS);
        //加载自定义函数
        require SYSTEM_PATH.'function.php';
        //获取请求参数并定义相应常量
        list($m, $c, $a) = self::getParams();
        define('MODULE', strtolower($m));
        define('CONTROLLER', strtolower($c));
        define('ACTION', strtolower($a));        
        define('MODULE_PATH', MODULES_PATH.MODULE.DS);
        define('CONTROLLER_PATH', MODULE_PATH.'controller'.DS);
        define('MODEL_PATH', MODULE_PATH.'model'.DS);
        define('VIEW_PATH', MODULE_PATH.'view'.DS);
        define('COMMON_VIEW', VIEW_PATH.'common'.DS);
        define('ACTION_VIEW', VIEW_PATH.CONTROLLER.DS.ACTION.'.html');
        //启动session
        session_start();
    }
    //自动加载文件
    private static function registerAutoLoad() {
        spl_autoload_register(function($class_name){
            $class_name = ucwords($class_name);
            if (strpos($class_name, 'Controller')) {
                $target = CONTROLLER_PATH."$class_name.class.php";
                if (is_file($target)) {
                    require $target;
                }else{
                    wmerror('您的访问参数有误！');
                }
             }elseif (strpos($class_name, 'Model')){
                require MODEL_PATH."$class_name.class.php";
            }else{
                require LIBRARY_PATH."$class_name.class.php";
            }
        });
    }
    //分发用户请求
    private static function dispatch() {
        $c = CONTROLLER.'Controller';
        $a = ACTION.'Action';
        //创建控制器对象
        $Controller = new $c();
        //调用控制器方法
        $Controller->$a();
    }
    //获取URL参数
    private static function getParams() {
        $m = wminput('m', 'get', 'string', 'default');
        $c = wminput('c', 'get', 'string', 'index');
        $a = wminput('a', 'get', 'string', 'index');
        return array($m, $c, $a);
    }
}

