<?php

class TestAppController extends Controller
{
    public function isAuthorized()
    {
        return true;
    }

    public function adefault()
    {
        $view = $this->getView();
        $view->assign('test', 'plop');
        $view->assign('param', $this->params['param']);
        return $view;
    }

    public function index()
    {
        $view = $this->getView();
        $view->assign('param', $this->params['param']);
        $view->assign('param2', $this->params['param2']);
        return $view;
    }

    public function post()
    {
        $view = $this->getView();
        $view->assign('email', $this->params['email']);
        return $view;
    }

    public function upload()
    {

    }
}
