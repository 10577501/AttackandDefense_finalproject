<?php

class ActivityController  extends Controller
{
    public function indexAction() {
        $model = new ActivityModel();
        $title = '专题活动';
        require ACTION_VIEW;
    }
}

