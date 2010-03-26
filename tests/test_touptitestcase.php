<?php

require_once(dirname(__FILE__).'/testapp/controller.php');

class TestTouptiTestCase extends TouptiTestCase
{
    public function touptiConf()
    {
        return array('route_path' => dirname(__FILE__) .'/testapp/routes.php');
    }

    public function viewSetup()
    {
        View::useLib('Mock');
    }

    public function testGet()
    {
        $view = $this->get('/?param=plip');
        $this->assertEqual($view->get('test') ,'plop');
        $this->assertEqual($view->get('param') ,'plip');
    }

    public function testGetWithMoreParam()
    {
        $view = $this->get('/index?param=plip&param2=plop');
        $this->assertEqual($view->get('param') ,'plip');
        $this->assertEqual($view->get('param2') ,'plop');
    }

    public function testPost()
    {
        $view = $this->post('/post', array('email' => 'test@example.net'));
        $this->assertEqual($view->get('email') ,'test@example.net');
    }
}
