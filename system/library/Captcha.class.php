<?php

/** 
 * @author weiwenping
 * 
 */
class Captcha
{

    // TODO - Insert your code here
    private $name = 'captcha';
    private $len = 5;
    private $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    
    /**
     */
    public function __construct()
    {
        
        // TODO - Insert your code here
    }
    
    public function create() {
        $im = imagecreate($x=250, $y=62);
        $bg = imagecolorallocate($im, rand(50,200), rand(0,155), rand(0,155));
        $fontColor = imagecolorallocate($im, 255, 255, 255);
        $fontStyle = LIBRARY_PATH.'font'.DS.'captcha.ttf';
        $captcha = $this->createCode();
        //生成指定长度的验证码
        for($i=0; $i<$this->len; ++$i){
            //随机生成字体颜色
            imagettftext (
                $im,        //画布资源
                30,  //文字大小
                mt_rand(0,20) - mt_rand(0,25),            //随机设置文字倾斜角度
                32+$i*40,mt_rand(30,50), //随机设置文字坐标，并自动计算间距
                $fontColor,  //文字颜色
                $fontStyle,  //文字字体
                $captcha[$i] 	 //文字内容
                );
        }
        isset($_SESSION) || session_start();
        $_SESSION[$this->name] = $captcha;
        //绘制干扰线
        for($i=0; $i<8; ++$i){
            //随机生成干扰线颜色
            $lineColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            //随机绘制干扰线
            imageline($im,mt_rand(0,$x),0,mt_rand(0,$x),$y,$lineColor);
        }
        //为验证码图片生成彩色噪点
        for($i=0; $i<250; ++$i){
            //随机绘制干扰点
            imagesetpixel($im,mt_rand(0,$x),mt_rand(0,$y),$fontColor);
        }
        header('Content-Type: image/gif'); //输出图像
        imagepng($im);
        imagedestroy($im);
    }
    
    private function createCode() {
        $code = '';
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->len; $i++) {
            $code .= $this->charset[mt_rand(0,$_len)];
        }
        return $code;
    }
    
    public function verify($input) {
        if (!empty($_SESSION[$this->name])) {
            $captcha = $_SESSION[$this->name];
            $_SESSION[$this->name] = '';
            return strtoupper($captcha) == strtoupper($input);
        }
        return false;
    }
}

