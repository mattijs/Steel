<?php

// Include path and autoloader
set_include_path('../lib' . PATH_SEPARATOR . get_include_path());
require_once 'Steel/Autoloader.php';
spl_autoload_register('Steel\Autoloader::autoload');

use \Steel\Router;

// Basic url
Router::map('/start', array(
    'controller' => 'start'
));

// With regular expression
Router::map('/page/{([0-9]+)}', array (
    'controller' => 'page',
    'action' => 'show',
));

// With named parameter
Router::map('/page/<slug>', array (
    'controller' => 'page',
    'action' => 'slug',
    'params' => array()
));

// Router::match returns the first matched route
$matched = Router::match('/page/');