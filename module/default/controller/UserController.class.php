<?php

class UserController  extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->model = new UserModel();
    }    
    public function indexAction() {
        if (!ISLOGIN) {
            header('Location:./?c=user&a=login');
            exit();
        }
        //个人信息项目设置
        $items = array(
            'name'=>'姓名',
            'gender'=>'性别',
            'role'=>'身份',
            'email'=>'邮箱',
            'description'=>'个人简介',
            'photo'=>'头像',
        );
        //获取用户详细信息
        $user = array('name'=>$this->user['name']);
        //判断文件是否存在
        $userinoFile = PUBLIC_PATH.'tmp/userinfo/'.$user['name'].'.txt';
        if(is_file($userinoFile)){
            //文件存在，从文件中读取用户数据，并与默认数据合并
            $user = array_merge($user,unserialize(file_get_contents($userinoFile)));
        }
        //处理头像数据
        $photo = PUBLIC_PATH.'uploads/photo/thumb_'.$this->user['id'].'.jpg';
        if (file_exists($photo)) {
            $img = PUBLIC_PATH.'uploads/photo/thumb_'.$this->user['id'].'.jpg';
        }else{
            $img = PUBLIC_PATH.'uploads/photo/default.png';
        }
        $photoHtml = "<img src='".$img."' />'";
        $user['photo'] = $photoHtml;
        
        $title = '用户中心';
        require ACTION_VIEW;
    }
    
    public function editAction(){        
        if (!ISLOGIN) {
            header('Location:./?c=user&a=login');
            exit();
        }
        $user = $this->user;
        //信息保存文件
        $userinoFile = PUBLIC_PATH.'tmp/userinfo/'.$user['name'].'.txt';
        //数据初始化
        $role = array('网站用户','志愿者','管理员','其它');
        $gender = array('男','女');
        //有表单提交时，接收表单数据并输出
        if($_POST){
            //定义需要接收的字段
            $fields = array('description', 'gender', 'role', 'email', 'gender');
            //通过循环自动接收数据并进行处理
            $user_data = array();  //用于保存处理结果
            foreach($fields as $v){
                $user_data[$v] = isset($_POST[$v]) ? $_POST[$v] : '';
            }
            //转义可能存在的HTML特殊字符
            $user_data['description'] = htmlspecialchars($user_data['description']);
            //验证性别是否为合法值
            if($user_data['gender']!='男' && $user_data['gender']!='女'){
                wmerror('保存失败，未选择性别。');
            }
            //验证身份是否为合法值
            if(!in_array($user_data['role'], $role)){
                wmerror('保存失败，您选择的身份不在允许的范围内。');
            }
            //验证邮箱是否为合法值
            if(!is_email($user_data['email'])){
                wmerror('保存失败，邮箱地址无效。');
            }
            //验证完成，保存文件
            //将数组序列化为字符串
            $data = serialize($user_data);            
            //将字符串保存到文件中 
            file_put_contents($userinoFile,$data);
            //保存成功
            $success = true;
        }
        
        //定义表单默认数据
        $user_data = array(
            'name' => $user['name'],
            'gender' => '男',
            'role' => '网站用户',
            'email'=> '',
            'description' => ''
        );
        //判断文件是否存在
        if(is_file($userinoFile)){
            //文件存在，从文件中读取用户数据，并与默认数据合并
            $user = array_merge($user_data,unserialize(file_get_contents($userinoFile)));
        }
        
        $title = '编辑用户信息';
        require ACTION_VIEW;
    }

    public function newsAction(){        
        if (!ISLOGIN) {
            header('Location:./?c=user&a=login');
            exit();
        }
        //有表单提交时，接收表单数据并输出
        if($_POST){
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $author = isset($_POST['author']) ? $_POST['author'] : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';

            header("Content-type: text/html; charset=utf-8");
            $dbms='mysql';
            $dbName='wwiweb';
            $user='root';
            $pwd='112169zxy';
            $host='localhost';
            $charset = 'utf8';
            $dsn="$dbms:host=$host;dbname=$dbName;charset=$charset";
            try{
                $pdo=new PDO($dsn,$user,$pwd);
                $pdo->exec('set names utf8');//设置编码
                //插入
                $sql ='insert into test_focus(id,title,author,pub,content,visits) values(null,"'.$title.'","'.$author.'","2021-06-13","'.$content.'",0)';
                $pdo->exec($sql);
                 //保存成功
                $success = true;
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
            //关闭连接
            $pdo = null;
           
        }
        
        $title = '资讯留言';
        require ACTION_VIEW;
    }
    
    public function photoAction(){
        //判断用户是否登录，如果登录，获取用户ID
        $user_id = checkLogin();  //如果没有登录，自动跳转到登录
        
        //根据用户id拼接头像文件保存路径
        $save_path = PUBLIC_PATH."uploads/photo/thumb_$user_id.jpg";        
        //判断是否上传头像
        if(isset($_FILES['pic'])){
            //获取用户上传文件信息
            $pic = $_FILES['pic'];
            //判断文件上传到临时文件时是否出错
            checkUpload($pic);
            //判断是否为合法的图片文件类型
            checkUploadPhoto($pic);
            //验证成功，为头像生成缩略图
            thumb(150,150,$pic['tmp_name'],$save_path);
        }
        
        $title = '用户头像上传';
        require ACTION_VIEW;
    }

    public function loginAction() {
        //判断是否为登录表单提交
        if ($_POST) {
            //接收表单字段
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
            //判断验证码是否正确
            if (!$this->_checkCaptcha($captcha)) {
                wmerror('验证码输入错误！');                 
            }
            //将用户名转换为小写
            $username = strtolower($username);
            //获取用户数据
            $user_data = $this->model->getData();
            //到用户数组中验证用户名和密码
            foreach($user_data as $key=>$v){
                if($v['name'] == $username && $v['password'] == $password){
                    //开启Session会话，将用户ID和用户名保存到Session中
                    $_SESSION['user'] = $v;
                    //重定向到用户中心个人信息页面
                    header('Location: ./?c=user');
                    exit;  //重定向后停止脚本继续执行
                }
            }
            wmerror('登录失败，用户名或密码错误！');  //验证失败
        }
        
        $title = '用户登录';
        require ACTION_VIEW;
    }
    public function registerAction() {
        //判断是否为注册表单提交
        if ($_POST) {
            //接收表单字段
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $repassword = isset($_POST['repassword']) ? $_POST['repassword'] : '';
            $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
            //判断验证码是否正确
            if (!$this->_checkCaptcha($captcha)) {
                wmerror('验证码输入错误！');                 
            }
            //将用户名转换为小写
            $username = strtolower($username);

            if(trim ($_POST['password'])!=trim($_POST['repassword'])){
                wmerror('两次密码不一致，请重输');
            }

            //获取用户数据
            $user_data = $this->model->getData();
            foreach($user_data as $key=>$v){
                if($v['name'] == $username){
                    wmerror('用户名已被占用，请更换其他用户名');
                }
                if($v['phone'] == $phone){
                    wmerror('手机号已被占用，请更换其他手机号');
                }
            }     
            
            $pattern = "/^[\w\x{4e00}-\x{9fa5}]{4,8}$/u";
            if (!preg_match($pattern, $_POST['username'])) {
                    
                wmerror('用户名格式不正确，用户名由4-8位数字、英文字母、汉字、下划线组成');
            }

            $pattern = "/^[a-zA-Z0-9]{6,12}$/";
                if (!preg_match($pattern, $_POST['password'])) {
                    wmerror('密码格式不正确，密码由6-12位数字、英文字母组成');
                } 

            $pattern = "/^0?(13|14|15|17|18)[0-9]{9}$/";
            if (!preg_match($pattern, $_POST['phone'])) {
                    wmerror('手机号码不正确');
            }     

            $data = $_POST ? $_POST : null;
            unset($data['repassword']);
            unset($data['captcha']);
            $id = $this->model ->addUser($data);                                   
            echo '<script language="JavaScript">;alert("注册成功，请重新登录！");location.href="./?c=user&a=login";</script>;';     
        }
        
        $title = '用户注册';
        require ACTION_VIEW;
    }
    

    public function logoutAction() {
        //清除Session中的用户信息
        unset($_SESSION['user']);
        //退出成功，自动跳转到主页
        header('Location: ./');
        exit;
    }
 
    public function captchaAction(){
        $captcha = new Captcha();
        $captcha->create();
    }
    private function _checkCaptcha($input){
        $captcha =  new Captcha();
        return $captcha->verify($input);
    }

    
}

