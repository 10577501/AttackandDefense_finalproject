<?php

class LoadController  extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->model = new LoadModel();
    }    
    public function uploadAction(){
        //判断用户是否登录，如果登录，获取用户ID
        $user_id = checkLogin();  //如果没有登录，自动跳转到登录

        header("content-type:text/html;charset=utf-8");
        error_reporting(E_ALL^E_NOTICE);

        if(!file_exists("./public/uploads/pic"))  //如果不存在该文件夹，则新建文件夹
        {
            mkdir("./public/uploads/pic");
        }
        $file=$_FILES['file'];//获取传输文件数组；

        foreach($file["name"] as $valuename){   //遍历文件['name数组'];得到字符串：1.png.2.png.3.png.;
           $value.=$valuename.".";        
        };
    
         $strr=substr($value,0,strlen($value)-1);   //字符串更新：1.png.2.png.3.png(去掉最后一个.);
     
        $strarr=explode(".", $strr);     //获得数组如：arr（1,png,2,png,3,png,4,png）;
     
        $filetypearr=[];
        foreach($strarr as $key=>$valuea){
            if($valuea!=""){                         //去掉上传过来的空值;
                if($key%2!=0){               
                $filetypearr[]=$valuea;             //获得新数组，如:arr(png,png,png);
            }
            }           
        }            
        $typearr=array("png","jpg","gif");   //可上传类型数组
        foreach($filetypearr as $key=>$valueb){        //判断格式类型是否正确;
            if(!checktype($typearr,$valueb)){
                //echo "<script language='JavaScript'>alert('文件格式不正确')</script>";
                wmerror('文件格式不正确！');   
            }
        } 

        for($i=0;$i<count($file['name']);$i++){
            $file['name'][$i]=iconv("utf-8","gb2312",$file['name'][$i]);
            if($file['size'][$i]>1024*200){                //判断文件大小是否符合，如果文件过大会提示该文件，符合的文件会继续上传，不符合的文件不上传;
                //echo "<script language='JavaScript'>alert('文件名为".$file['name'][$i]."的文件过大');</script>";
                wmerror('文件过大！');       
        }else{                                          //存储文件，并跳转到文件展示页面;
            $strfile=explode(".",$file["name"][$i]);     //获得数组如：arr（1,png,2,png,3,png,4,png）;
            foreach($strfile as $key=>$valuefile){
                if($valuefile!=""){                         //去掉上传过来的空值;
                    if($key%2!=0){               
                    $strfiletype=$valuefile;             //获得新数组，如:arr(png,png,png);
                }
                }           
            }     
            
            $file["name"][$i]=uniqid().".".$strfiletype;
        move_uploaded_file($file['tmp_name'][$i], "./public/uploads/pic/".$file["name"][$i]);
        header('Location: ./?c=load&a=download');
    }            
}       

        $title = '上传';
        require ACTION_VIEW;
    }

    public function downloadAction(){
        //判断用户是否登录，如果登录，获取用户ID
        $user_id = checkLogin();  //如果没有登录，自动跳转到登录
        header("content-type:text/html;charset=utf-8");
        $picarr=scandir("./public/uploads/pic/");   //获取文件夹内的所有文件；
        ini_set('date.timezone','Asia/Shanghai'); //时区设置，东八区上海时间；
        
        error_reporting(E_ALL^E_NOTICE);
        if ( $_REQUEST['delete'] ) {   //一个表单多个提交按钮，区分提交按钮的name值;
            $picname=$_POST['del'];
            foreach($picname as $value){
                unlink("./public/uploads/pic/$value");          //删除对应的文件;
            }         
        } 
        else if($_REQUEST['download']){    
            $picname=$_POST['del'];                    
            if($picname!=''){                     //去掉空值的情况（没有选中任何项，无表单传值）
                foreach($picname as $value){
                    if(!file_exists("./public/uploads/pic/$value")){        //没有该文件时无法下载;
                        //echo "<script language='JavaScript'>alert('没有该文件，无法下载')</script>";
                        wmerror("没有该文件，无法下载");
                }else{
                    $fp=fopen("./public/uploads/pic/$value","r");               //打开文件指针；
                    $file_size=filesize("./public/uploads/pic/$value");         //文件的大小;   
                    Header("Content-type: application/octet-stream");  //告知浏览器下载的文件类型;
                    Header("Accept-Ranges: bytes");             //返回的文件大小按照字节计算; 
                    Header("Accept-Length:".$file_size);        //返回的文件大小;
                    Header("Content-Disposition: attachment; filename=".$value);   //返回的文件的名称;
                    $buffer=1024;
                    $file_count=0; 
                    while(!feof($fp) && $file_count<$file_size){
                        $file_con=fread($fp,$buffer);
                        $file_count+=$buffer;
                        echo $file_con;
                    }
                    fclose($fp);        
                    }    
                }    
            }    
    } 
    elseif ($_REQUEST['downloadall'] ) {     //一个表单多个提交按钮，区分提交按钮的name值;    
        $picname=$_POST['del'];                    
        if($picname!=''){                     //去掉空值的情况（没有选中任何项，无表单传值）
            $filename = "./public/uploads/pic/download.zip"; //最终生成的文件名（含路径） 
            if(!file_exists($filename)){      //重新生成文件      
                $zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释  
                if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {  
                    wmerror('无法打开文件，或者文件创建失败');
                }  
                foreach( $picname as $val){  
                    if(file_exists("./public/uploads/pic/$val")){  
                        $zip->addFile( "./public/uploads/pic/$val", basename($val));
                        //第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下  
                    }  
                }  
                $zip->close();//关闭  
            }  
            if(!file_exists($filename)){  
                wmerror('无法找到文件');   
            }  
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename($filename)); //文件名  
            header("Content-Type: application/zip"); //zip格式的  
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件  
            header('Content-Length: '. filesize($filename)); //告诉浏览器，文件大小  
            @readfile($filename);    
            unlink($filename);
        }    
    }

               
        $title = '下载';
        require ACTION_VIEW;
    }


    
}

