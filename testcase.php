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
    /**
     * Perform a get request
     */
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
    /**
     * Perform a post request
     */
    public function post($url, array $params)
    {
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->handleFiles($params);
        $_POST = $params;
        $this->currenturl = $url;
        $this->toupti = Toupti::instance($this->toupticonf);
        return $this->toupti->run();
    }

    private function handleFiles(array $params)
    {
        foreach ($params as $key => $p)
        {
            if (is_array($p))
            {
                $_FILES[$key] = array('name' => array(), 'size' => array(), 'type' => array(), 'error' => array(), 'tmp_name' => array());
                foreach ($p as $i => $ar)
                {
                    if (is_resource($ar))
                    {
                        $file = $this->prepareUploadFile($ar);
                        $_FILES[$key]['name'][$i]     = $file['name'];
                        $_FILES[$key]['size'][$i]     = $file['size'];
                        $_FILES[$key]['type'][$i]     = $file['type'];
                        $_FILES[$key]['tmp_name'][$i] = $file['tmp_name'];
                        $_FILES[$key]['error'][$i]    = $file['error'];
                    }
                }
            }
            elseif (is_resource($p))
            {
                $_FILES[$key] = $this->prepareUploadFile($p);
            }
        }
    }

    private function prepareUploadFile($resource)
    {
        $meta_data = stream_get_meta_data($resource);
        $uri = $meta_data['uri'];
        $file = array();
        $file['name'] = basename($uri);
        $file['size'] = filesize($uri);
        $file['type'] = mime_content_type($uri);
        $tmp = tempnam(sys_get_temp_dir(), basename($uri));
        copy($uri, $tmp);
        $file['tmp_name'] = $tmp;
        $file['error'] = UPLOAD_ERR_OK;
        return $file;
    }
}
