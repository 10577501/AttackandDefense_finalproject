<?php
function wminput($var, $method='post',$type='text',$def='') {
    switch ($method) {
        case 'get':$method = &$_GET;break;
        case 'post':$method = &$_POST;break;
        case 'cookie':$method = &$_COOKIE;break;
        case 'server':$method = &$_SERVER;break;
    }
    $value = isset($method[$var]) ? $method[$var] : $def;
    switch ($type) {
        case 'string':
            $value = is_string($value) ? $value : '';
            break;
        case 'text':
            $value = is_string($value) ? trim(htmlentities($value)) : '';
            break;
        case 'int':
            $value = (int)$value;
            break;
        case 'id':
            $value = max((int)$value,0);
            break;
        case 'float':
            $value = (float)$value;
            break;
        case 'bool':
            $value = (bool)$value;
            break;
    }
    return $value;
}

function wmerror($msg) {
    header('content-type:text/html;charset=utf-8');
    //die('<pre>'.htmlentities($msg).'</pre>');
    exit('<script>alert("'.htmlspecialchars($msg).'");history.back();</script>');
}
//判断用户是否登录
function checkLogin(){
    //当用户没有登录时，重定向到登录页面
    if(!isset($_SESSION['user'])){
        header('Location: ./?c=user&a=login');
        exit; //停止脚本文件继续执行
    }
    //用户已登录，返回用户ID
    return isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
}
//判断上传是否成功
function checkUpload($file){
    if($file['error'] > 0){
        $error = '上传失败：';
        switch($file['error']){
            case 1: $error .= '文件大小超过了服务器设置的限制！';break;
            case 2: $error .= '文件大小超过了表单设置的限制！'; break;
            case 3: $error .= '文件只有部分被上传！'; break;
            case 4: $error .= '没有文件被上传！'; break;
            case 6: $error .= '上传文件临时目录不存在！'; break;
            case 7: $error .= '文件写入失败！'; break;
            default: $error .='未知错误！'; break;
        }
        wmerror($error);  //显示错误信息并停止脚本
    }
}
//判断文件类型
function checkUploadPhoto($file){
    //判断是否为允许的图片格式
    $type = strrchr($file['name'],'.');
    if(($type !== '.jpg') || ($file['type'] !== 'image/jpeg')){
        wmerror('图像类型不符合要求，只支持jpg类型的图片');
    }
}
//为上传头像生成缩略图
function thumb($max_width,$max_height,$file_path,$save_path){
    list($width, $height) = getimagesize($file_path);
    //等比例计算缩略图的宽和高
    if($width/$max_width > $height/$max_height) {
        //宽度大于高度时，将宽度限制为最大宽度，然后计算高度值
        $new_width = $max_width;
        $new_height = round($new_width / $width * $height);
    }else{
        //高度大于宽度时，将高度限制为最大高度，然后计算宽度值
        $new_height = $max_height;
        $new_width = round($new_height / $height * $width);
    }
    //绘制缩略图的画布资源
    $thumb = imagecreatetruecolor($new_width, $new_height);
    //从文件中读取出图像，创建为jpeg格式的图像资源
    $source = imagecreatefromjpeg($file_path);
    //将原图缩放填充到缩略图画布中
    imagecopyresized($thumb,$source,0,0,0,0,$new_width,$new_height,$width,$height);
    //将保存缩略图到指定目录（参数依次为图像资源、保存目录、输出质量0~100）
    imagejpeg($thumb, $save_path, 100);
}
function checktype($typearr,$type){   //判断类型方法;
    foreach($typearr as $value){
        if($value==$type){
            return true;
        }
}
return false;
}
function format_bytes($size) 
    {             //文件大小转换方法（字节转换为KB,MB,GB,TB）;
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2).$units[$i];
    } 
function is_email($email){ 
        $pattern="/^([\w\.-]+)@([a-zA-Z0-9-]+)(\.[a-zA-Z\.]+)$/i";//包含字母、数字、下划线_和点.的名字的email 
        if(preg_match($pattern,$email)){ 
            return true; 
        }else{ 
            return false; 
        } 
    } 
?>
