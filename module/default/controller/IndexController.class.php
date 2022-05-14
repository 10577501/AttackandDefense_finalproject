<?php

class IndexController  extends Controller
{
    public function indexAction() {
        //最新动态版块
        $model = new FocusModel();
        $data = $model->getData();
        //首页动态版块数据
        if (count($data) > 5) {
            $data = array_slice($data, 0, 5);
        }
        $title = '首页';
        require ACTION_VIEW;
    }
}

