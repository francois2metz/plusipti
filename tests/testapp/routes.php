<?php

$routes = HighwayToHeaven::instance();

$routes->add('', array('controller' => 'TestApp'));
$routes->add('index', array('controller' => 'TestApp', 'action' => 'index'));
$routes->add('post', array('controller' => 'TestApp', 'action' => 'post'));
$routes->add('upload', array('controller' => 'TestApp', 'action' => 'upload'));
