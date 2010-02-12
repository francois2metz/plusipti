<?php

/**
 * @package Toupti
 */
class View
{

    protected static $conf = array(
        'template_dir' => '',
        'compile_dir' => '',
        'cache_dir' => '',
        'config_dir' => '',
        'plugins_dir' => ''
    );

    public $smarty = null;

    public $_js = array();

    public $_notifs = array();

    public static function conf($conf) 
    {
        self::$conf = $conf;
    }

    function __construct($tpl = '', $params = array())
    {
        $this->smarty = new Smarty();
        $this->smarty->template_dir = self::$conf['template_dir'];
        $this->smarty->compile_dir = self::$conf['compile_dir'];
        $this->smarty->cache_dir = self::$conf['cache_dir'];
        $this->smarty->config_dir = self::$conf['config_dir'];
        $this->smarty->plugins_dir []= self::$conf['plugins_dir'];

        $this->tpl = $tpl;
        $this->params =  $params;
        $this->assign('params', $this->params);
    }

    public function assign($key, $value)
    {
        if($value instanceof View)
        {
            $this->notify($value->getNotifs());
            $value = $value->fetch();
        }
        $this->smarty->assign($key, $value);
    }

    /**
     * @return Array notifs accumulator.
     */
    public function getNotifs()
    {
        return $this->_notifs;
    }

    /**
     * @param ApiException or an array of them.
     */
    public function notify($notify)
    {
        if(is_array($notify))
        {
            foreach($notify as $key => $value)
                $this->addNotify($value);
        }
        else
        {
            $this->addNotify($notify);
        }
    }

    private function addNotify($notify)
    {
        if (!is_null($notify))
            $this->_notifs[]= $notify;
    }

    /**
     * Required javascript file.
     * @param $files String  needed javascript file.
     */
    public function js()
    {
        $args = func_get_args();
        $this->_js = array_merge($this->_js, $args);
    }

    public function display($tpl = null)
    {
        if(!is_null($tpl))
        {
            $this->tpl = $tpl;
        }
        $this->smarty->display($this->tpl);
    }

    public function fetch($tpl = null)
    {
        if(!is_null($tpl))
        {
            $this->tpl = $tpl;
        }
        return $this->smarty->fetch($this->tpl);
    }

}
