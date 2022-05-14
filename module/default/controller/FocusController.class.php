<?php

class FocusController  extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->model = new FocusModel();
    }
    public function indexAction() {
        //最新动态版块
        $data = $this->model->getData();
        //处理分页
        //获取当前访问的页码
        $page = isset($_GET['page']) ? (int)$_GET['page'] :  1;
        //获取总记录数
        $total = count($data);
        //实例化分页类
        $pageObj = new Page($total,3,$page); //page(总页数，每页显示条数，当前页)
        //获取limit条件
        $limit = $pageObj->getLimit();
        //处理页面数据
        if ($total > 3) {
            $data = array_slice($data, $limit, 3);
        }
        //获取分页HTML链接
        $pageBar = $pageObj->showPage();
        $title = '近期关注';
        require ACTION_VIEW;
    }
    public function detailAction() {
        $id = $_GET['id'];
        $data = $this->model->getDataById($id);
        //更新浏览次数
        $this->model->vists($id);
        $title = '关注详情';
        require ACTION_VIEW;
    }
}

