<?php

/**
 * @package Toupti
 */
class View
{

    protected static $view_class = NULL;

    protected $view_object = NULL;

    protected static $conf = array(
    );

    public $_js = array();

    public $_notifs = array();

    public static function conf($conf) 
    {
        self::$conf = $conf;
    }

    public static function useLib($lib)
    {
        $class_name = ucfirst(strtolower($lib)).'View';
        if(!class_exists($class_name)) throw new TouptiException(sprintf("The %s view adpator could not be loaded", $lib));
        self::$view_class = $class_name;
    }

    public function __construct($tpl = '', $params = array())
    {
        $view_class = self::$view_class;
        
        call_user_func_array(array($view_class, 'conf'), array(self::$conf));
        $this->view_object = new $view_class($tpl, $params);
    }

    public function __call($name, $arguments)
    {
        if(is_null($this->view_object)) throw TouptiException(sprintf("Could not call %s either on View nor on a Lib"));
        return $this->view_object->$name($arguments);
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
}
