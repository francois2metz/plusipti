<?php

abstract class ViewAdaptor
{
    
    abstract public static function conf($conf); 

    abstract public function __construct($tpl = '', $params = array());

    abstract public function assign($key, $value);

    abstract public function display($tpl = null);

    abstract public function fetch($tpl = null);
}
