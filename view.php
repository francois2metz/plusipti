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

    public static function conf($conf) 
    {
        self::$conf = $conf;
    }

    public static function useLib($lib)
    {
        $class_name = ucfirst(strtolower($lib)).'View';
        if (!class_exists($class_name)) 
        {
            throw new TouptiException(sprintf("The %s view adpator could not be loaded", $lib));
        }
        self::$view_class = $class_name;
    }

    public function __construct($tpl = '', $params = array())
    {
        $view_class = self::$view_class;
        if (is_null($view_class))
        { 
            throw TouptiException("no adaptor set");
        }
        call_user_func_array(array($view_class, 'conf'), array(self::$conf));
        $this->view_object = new $view_class($tpl, $params);
    }

    public function __call($name, $arguments)
    {
        if (is_null($this->view_object))
        { 
            throw TouptiException(sprintf("Could not call %s either on View nor on a Lib", $name));
        }
        return call_user_func_array(array($this->view_object, $name), $arguments);
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
