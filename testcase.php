<?php

/**
 * SimpleTest testcase for testing toupti application
 * It's like Simpletest WebTester
 * @package Toupti
 */
abstract class TouptiTestCase extends UnitTestCase
{
    private $toupticonf;

    private $currenturl;

    final public function setUp()
    {
        Toupti::destroy();
        HighwayToHeaven::destroy();
        View::reset();
        $_SERVER  = array();
        $_GET     = array();
        $_POST    = array();
        $_REQUEST = array();
        $_FILES   = array();
        $this->toupticonf = $this->touptiConf();
        $this->viewSetUp();
    }

    final public function tearDown()
    {
        Toupti::destroy();
        HighwayToHeaven::destroy();
        View::reset();
    }

    abstract function touptiConf();

    abstract function viewSetUp();

    public function getUrl()
    {
        return $this->currenturl;
    }

    public function get($url)
    {
        if ($offset = strpos($url, '?'))
        {
            $get = substr($url, $offset+1);
            $params = explode('&', $get);
            foreach ($params as $param)
            {
                $p = explode('=', $param);
                $_GET[$p[0]] = $p[1];
            }
        }
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->currenturl = $url;
        $this->toupti = Toupti::instance($this->toupticonf);
        return $this->toupti->run();
    }

    public function post($url, $params)
    {
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $params;
        $this->currenturl = $url;
        $this->toupti = Toupti::instance($this->toupticonf);
        return $this->toupti->run();
    }
}
