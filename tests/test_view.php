<?php

require_once('simpletest/autorun.php');
require_once('../toupti.php');
require_once('../view.php');
require_once('../view_libs/adaptor.php');
require_once('../view_libs/smarty.php');

class MockView extends ViewAdaptor
{
    public static $var = array();
    public static $conf_setup = false;
    public static function conf($conf)
    {
        self::$conf_setup = true;
    }

    public function __construct($tpl, $params)
    {
    }
    public function assign($key, $value)
    {
        self::$var[$key] = $value;
    }
    public function display($tpl = null)
    {
    }
    public function fetch($tpl = null)
    {
    }
}

class TestView extends UnitTestCase
{
    public function setUp()
    {
        View::reset();
    }
    
    public function testCreateInstanceWithoutCallToUseLib()
    {
        $this->expectException(new TouptiException('no adaptor set'));
        new View();
    }

    public function testCallUseLibWithBadClass()
    {
        $this->expectException(new TouptiException('The foobar view adaptor could not be loaded, class name FoobarView'));
        View::useLib('foobar');
    }

    public function testCreateView()
    {
        $this->assertFalse(MockView::$conf_setup);
        View::useLib('Mock');
        $view = new View();
        $this->assertTrue(MockView::$conf_setup);
    }

    public function testCallMethodOnView()
    {
        View::useLib('Mock');
        $view = new View();
        $view->assign('chuck', 'norris');
        $this->assertEqual(1, count(MockView::$var));
        $this->assertEqual('norris', MockView::$var['chuck']);
    }

    public function testBadCallMethodOnView()
    {
        View::useLib('Mock');
        $view = new View();
        $this->expectException(new TouptiException('Could not call assign2 either on View nor on a Lib'));
        $view->assign2('chuck', 'norris');
    }

    public function testAddJavascript()
    {
        View::useLib('Mock');
        $view = new View();
        $view->js(array('test.js'));
        $this->assertEqual(1, count($view->_js));
    }
}