<?php

// Include path and autoloader
set_include_path(dirname(__DIR__) . '/lib' . PATH_SEPARATOR . get_include_path());
require_once 'steel/Autoloader.php';
spl_autoload_register('steel\Autoloader::autoload');

use \steel\net\http\Router as Router;

// Basic url
Router::map('/forum/users', array(
	'controller' => 'users',
	'action' => 'list'
), 'forum_users');

// Named parameters
Router::map('/forum/{fid}', array (
    'controller' => 'forum',
    'action' => 'show'
), 'show_forum');

Router::map('/forum/{fid}/topic/{tid}', array(
    'controller' => 'topic',
    'action' => 'show'
), 'show_topic');

// With requirements
Router::map('/page/{id:[0-9]+}', array (
    'controller' => 'page',
    'action' => 'show',
), 'show_page');

Router::map('/page/{slug}', array (
    'controller' => 'page',
    'action' => 'show',
    'requirements' => array(
        'slug' => '[\W -_]+'
    ),
), 'show_page_by_slug');

// Retrieve a route by name
$route = Router::getRoute('show_topic');

// Router::match returns the first matched route
$matched = Router::match('/forum/1234/topic/the-title');

/*
// Limit the request method
Router::map('/user/update', array(
	'controller' => 'user',
	'action' => 'update',
	'methods' => array('POST', 'PUT')
));
*/
