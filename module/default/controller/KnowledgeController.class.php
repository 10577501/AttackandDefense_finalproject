<?php

class KnowledgeController  extends Controller
{
    public function AsianAction() {
        $model = new KnowledgeModel();
        $title = '亚洲野生动物';
        require ACTION_VIEW;
    }
    public function AfricaAction() {
        $model = new KnowledgeModel();
        $title = '非洲野生动物';
        require ACTION_VIEW;
    }
    public function AmericaAction() {
        $model = new KnowledgeModel();
        $title = '美洲野生动物';
        require ACTION_VIEW;
    }
    public function OceaniaAction() {
        $model = new KnowledgeModel();
        $title = '大洋洲野生动物';
        require ACTION_VIEW;
    }
    public function OtherAction() {
        $model = new KnowledgeModel();
        $title = '其他野生动物';
        require ACTION_VIEW;
    }
}

