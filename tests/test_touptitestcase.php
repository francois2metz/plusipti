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
        $this->assertEqual($this->getUrl(), '/?param=plip');
    }

    public function testGetWithMoreParam()
    {
        $view = $this->get('/index?param=plip&param2=plop');
        $this->assertEqual($view->get('param') ,'plip');
        $this->assertEqual($view->get('param2') ,'plop');
        $this->assertEqual($this->getUrl(), '/index?param=plip&param2=plop');
    }

    public function testPost()
    {
        $view = $this->post('/post', array('email' => 'test@example.net'));
        $this->assertEqual($view->get('email') ,'test@example.net');
        $this->assertEqual($this->getUrl(), '/post');
    }

    public function testPostFile()
    {
        $filepath = dirname(__FILE__).'/data/test.pdf'; 
        $f = fopen($filepath, 'r');
        $this->post('/upload', array('file' => $f));
        $this->assertEqual($_FILES['file']['name'], 'test.pdf');
        $this->assertEqual($_FILES['file']['size'], filesize($filepath));
        $this->assertEqual($_FILES['file']['type'], 'application/pdf');
        $this->assertTrue(file_exists($_FILES['file']['tmp_name']));
        $this->assertEqual($_FILES['file']['error'], UPLOAD_ERR_OK);
        fclose($f);
    }

    public function testPostMultipleFile()
    {
        $filepath = dirname(__FILE__).'/data/test.pdf'; 
        $f = fopen($filepath, 'r');
        $this->post('/upload', array('file' => array($f, $f)));
        $this->assertEqual($_FILES['file']['name'][0], 'test.pdf');
        $this->assertEqual($_FILES['file']['size'][0], filesize($filepath));
        $this->assertEqual($_FILES['file']['type'][0], 'application/pdf');
        $this->assertTrue(file_exists($_FILES['file']['tmp_name'][0]));
        $this->assertEqual($_FILES['file']['error'][0], UPLOAD_ERR_OK);
        $this->assertEqual($_FILES['file']['name'][1], 'test.pdf');
        fclose($f);
    }
}
