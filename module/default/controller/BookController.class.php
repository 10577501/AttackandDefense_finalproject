<?php

class BookController extends Controller
{
    
    public function indexAction() {
        
        $title = '主编教材';
        //最新动态版块
        $model = new BookModel();
        $data = $model->getData();
        //处理分页
        //获取当前访问的页码
        $page = isset($_GET['page']) ? (int)$_GET['page'] :  1;
        //获取总记录数
        $total = count($data);
        //实例化分页类
        $pageObj = new Page($total,2,$page); //page(总页数，每页显示条数，当前页)
        //获取limit条件
        $limit = $pageObj->getLimit();
        //处理页面数据
        if ($total > 2) {
            $data = array_slice($data, $limit, 2);
        }
        //获取分页HTML链接
        $pageBar = $pageObj->showPage();
        require ACTION_VIEW;
    }
    
}

