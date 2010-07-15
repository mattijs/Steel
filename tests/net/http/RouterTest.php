<?php
/**
 * class RouterTest
 * @package Steel
 * @category tests
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/mattijs/Steel/raw/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to the copyright holder so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010 Mattijs Hoitink
 * @license http://github.com/mattijs/Steel/raw/master/LICENSE New BSD License
 */

require_once 'PHPUnit/Framework.php';

use steel\core\Loader as Loader;
use steel\net\http\Router as Router;

/**
 * Test cases for steel\net\http\Router.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class RouterTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up the tests requirements. Sets the include path and registers the
     * steel\Loader to autoload Steel library files.
     */
    public function setUp()
    {
        // Setup include path
        set_include_path(realpath(__DIR__ . '/../../../lib') . PATH_SEPARATOR . get_include_path());
        
        // Register Steel autloader
        require_once 'steel/core/Loader.php';
        Loader::registerAutoload();
        
        // Clear all mapped routes
        Router::clear();
    }

    /**
     * Test the map function for static patterns.
     *
     * - Test normal mapping
     * - Test adding another normal route
     * - Test mapping without leading slash
     * - Test mapping with trailing slash
     */
    public function testMapStatic()
    {
        // Test normal mapping
        Router::map('/start', array(
            'controller' => 'start',
            'action' => 'go'
        ), 'start');
        $this->assertEquals(1, count(Router::getRoutes()));
        
        $route = Router::getRoute('start');
        $this->assertNotNull($route);
        $this->assertEquals('^/start(?:/?)$', $route['pattern']);

        // Test adding another normal route
        Router::map('/end', array(
            'controller' => 'end',
            'action' => 'stop'
        ), 'end');
        $this->assertEquals(2, count(Router::getRoutes()));
        
        $route = Router::getRoute('end');
        $this->assertNotNull($route);
        $this->assertEquals('^/end(?:/?)$', $route['pattern']);
        
        // Test mapping without leading slash
        Router::map('no/leading/slash', array (
            'controller' => 'default',
            'action' => 'default'
        ), 'no_leading_slash');
        $this->assertEquals(3, count(Router::getRoutes()));
        
        $route = Router::getRoute('no_leading_slash');
        $this->assertNotNull($route);
        $this->assertEquals('^/no/leading/slash(?:/?)$', $route['pattern']);
        
        // Test mapping with trailing slash
        Router::map('/with/trailing/slash/', array (
            'controller' => 'default',
            'action' => 'default'
        ), 'with_trailing_slash');
        $this->assertEquals(4, count(Router::getRoutes()));
        
        $route = Router::getRoute('with_trailing_slash');
        $this->assertNotNull($route);
        $this->assertEquals('^/with/trailing/slash(?:/?)$', $route['pattern']);
    }
    
    /**
     * Test the map function for named parameter patterns.
     *
     * - Test one parameter
     * - Test multiple parameters
     */
    public function testMapNamedParameters()
    {
        // Test one parameter
    	Router::map('/page/{id}', array(
    	    'controller' => 'page',
    	    'action' => 'show'
    	), 'show_page');
    	
    	$route = Router::getRoute('show_page');
    	$this->assertNotNull($route);
    	$this->assertEquals(1, count($route['_paramMap']));
    	$this->assertEquals(array(1 => 'id'), $route['_paramMap']);
    	$this->assertEquals('^/page/([^/]+)(?:/?)$', $route['pattern']);
    	
    	// Test multiple parameters
        Router::map('/forum/{fid}/topic/{tid}', array (
            'controller' => 'forum',
            'action' => 'topic'
        ), 'topic');
        
        $route = Router::getRoute('topic');
        $this->assertNotNull($route);
        $this->assertEquals(2, count($route['_paramMap']));
        $this->assertEquals(array(1 => 'fid', 3 => 'tid'), $route['_paramMap']);
        $this->assertEquals('^/forum/([^/]+)/topic/([^/]+)(?:/?)$', $route['pattern']);
    }
    
    /**
     * Test the map function for patterns containing regular expressions.
     *
     * - Test inline requirement
     * - Test inline requirement containing curly brackets
     * - Test multiple inline requirements
     * - Test mapping with requirements array
     * - Test requirement priority
     */
    public function testMapWithRequirements()
    {
        // Test inline requirement
        Router::map('/forum/{fid:[0-9]+}', array(
    	    'controller' => 'forum',
    	    'action' => 'show'
    	), 'show_forum');
    	
    	$route = Router::getRoute('show_forum');
    	$this->assertNotNull($route);
    	$this->assertEquals(1, count($route['_paramMap']));
		$this->assertEquals(array(1 => 'fid'), $route['_paramMap']);
    	$this->assertEquals('^/forum/([0-9]+)(?:/?)$', $route['pattern']);
    	
    	// Test inline requirement containing curly brackets
    	Router::map('/user/{uid:[\W]{1,2}}', array(
    	    'controller' => 'user',
    	    'action' => 'show'
    	), 'show_user');
    	
    	$route = Router::getRoute('show_user');
    	$this->assertNotNull($route);
    	$this->assertEquals(1, count($route['_paramMap']));
		$this->assertEquals(array(1 => 'uid'), $route['_paramMap']);
    	$this->assertEquals('^/user/([\W]{1,2})(?:/?)$', $route['pattern']);
    	
    	// Test multiple inline requirements
    	Router::map('/forum/{fid:[0-9]+}/topic/{topic_slug:\W+}', array (
    	    'controller' => 'forum',
    	    'action' => 'topic'
        ), 'show_topic');
        
        $route = Router::getRoute('show_topic');
        $this->assertNotNull($route);
    	$this->assertEquals(2, count($route['_paramMap']));
		$this->assertEquals(array(1 => 'fid', 3 => 'topic_slug'), $route['_paramMap']);
    	$this->assertEquals('^/forum/([0-9]+)/topic/(\W+)(?:/?)$', $route['pattern']);

        // Test requirements array
        Router::map('/page/{pid}/{pslug}', array (
            'controller' => 'page',
            'action' => 'show',
            'requirements' => array (
                'pid' => '[0-9]+',
                'pslug' => '[\W -_]+'
            ),
        ), 'show_page');
        $route = Router::getRoute('show_page');
        $this->assertNotNull($route);
        $this->assertEquals(array(1 => 'pid', 2 => 'pslug'), $route['_paramMap']);
    	$this->assertEquals('^/page/([0-9]+)/([\W -_]+)(?:/?)$', $route['pattern']);

        // Test requirement priority
        Router::map('/profile/{user:[\W]+}', array (
            'controller' => 'profile',
            'action' => 'show',
            'requirements' => array (
                'user' => '[0-9]+'
            )
        ), 'show_profile');
        $route = Router::getRoute('show_profile');
        $this->assertNotNull($route);
        $this->assertEquals(array(1 => 'user'), $route['_paramMap']);
    	$this->assertEquals('^/profile/([0-9]+)(?:/?)$', $route['pattern']);
    }
    
    /**
     * Test the matching of static urls.
     *
     * - Test non matching url
     * - Test matching url
     * - Test matching a second url
     */
    public function testMatchStatic()
    {
        // Test non matching url
        Router::map('/start', array(
            'controller' => 'start',
            'action' => 'go'
        ), 'start');
        $this->assertEquals(1, count(Router::getRoutes()));
        $route = Router::match('/end');
        $this->assertEquals(false, $route);
        
        // Test matching url
        $route = Router::match('/start');
        $this->assertNotNull($route);
        $this->assertEquals('/start', $route['matched_url']);
        $this->assertEquals('^/start(?:/?)$', $route['pattern']);
        $this->assertEquals('start', $route['controller']);
        $this->assertEquals('go', $route['action']);
        
        // Test matching a second url
        Router::map('/end', array (
            'controller' => 'end',
            'action' => 'die',
        ), 'end');
        $this->assertEquals(2, count(Router::getRoutes()));
        
        $route = Router::match('/end');
        $this->assertNotNull($route);
        $this->assertEquals('/end', $route['matched_url']);
        $this->assertEquals('^/end(?:/?)$', $route['pattern']);
        $this->assertEquals('end', $route['controller']);
        $this->assertEquals('die', $route['action']);
    }
    
    /**
     * Test the matching of named parameters.
     *
     * - Test non matching url
     * - Test matching url
     * - Test matching with multiple named parameters
     */
    public function testMatchNamedParameters()
    {
        // Test non matching url
        Router::map('/forum/{fid}', array (
            'controller' => 'forum',
            'action' => 'show',
        ), 'show_forum');
        $this->assertEquals(1, count(Router::getRoutes()));
        $route = Router::match('/topic/1234');
        $this->assertEquals(false, $route);
        
        // Test matching url
        $route = Router::match('/forum/5678');
        $this->assertNotNull($route);
        $this->assertEquals('/forum/5678', $route['matched_url']);
        $this->assertEquals(2, count($route['params']));
        $this->assertEquals(array(1 => '5678', 'fid' => '5678'), $route['params']);
        $this->assertEquals('^/forum/([^/]+)(?:/?)$', $route['pattern']);
        $this->assertEquals('forum', $route['controller']);
        $this->assertEquals('show', $route['action']);
        
        // Test matching with multiple named parameters
        Router::map('/forum/{fid}/topic/{tid}', array (
            'controller' => 'topic',
            'action' => 'show'
        ), 'show_topic');
        
        $route = Router::match('/forum/1234/topic/4321');
        $this->assertNotNull($route);
        $this->assertEquals('/forum/1234/topic/4321', $route['matched_url']);
        $this->assertEquals(4, count($route['params']));
        $this->assertEquals(array(1 => '1234', 'fid' => '1234', 3 => '4321', 'tid' => 4321), $route['params']);
        $this->assertEquals('^/forum/([^/]+)/topic/([^/]+)(?:/?)$', $route['pattern']);
        $this->assertEquals('topic', $route['controller']);
        $this->assertEquals('show', $route['action']);
    }
    
    /**
     * Test the matching of regex parameters.
     *
     * - Test single inline requirement
     * - Test multiple inline requirements
     * - Test single requirement in array
     * - Test multiple requirements in array
     * - Test requirement priority
     */
    public function testMatchWithRequirements()
    {
        // Test single inline requirement
        Router::map('/page/{id:[0-9]+}', array(
    	    'controller' => 'page',
    	    'action' => 'show'
    	), 'show_page');
    	$this->assertEquals(1, count(Router::getRoutes()));
    	
    	$route = Router::match('/page/a-page-slug');
    	$this->assertEquals(false, $route);
    	
    	$route = Router::match('/page/1');
    	$this->assertNotNull($route);
    	$this->assertEquals('/page/1', $route['matched_url']);
    	$this->assertEquals(2, count($route['params']));
    	$this->assertEquals('^/page/([0-9]+)(?:/?)$', $route['pattern']);
    	$this->assertEquals('page', $route['controller']);
    	$this->assertEquals('show', $route['action']);
    	
    	// Test multiple inline requirements
    	Router::map('/forum/{fid:[0-9]+}/topic/{slug:[a-zA-Z\-]+}', array (
    	    'controller' => 'topic',
    	    'action' => 'show'
    	), 'show_topic');
    	
    	$route = Router::match('/forum/1234/topic/4321');
    	$this->assertEquals(false, $route);
    	
    	$route = Router::match('/forum/1234/topic/the-title');
    	$this->assertNotNull($route);
    	$this->assertEquals('/forum/1234/topic/the-title', $route['matched_url']);
    	$this->assertEquals(4, count($route['params']));
    	$this->assertEquals('^/forum/([0-9]+)/topic/([a-zA-Z\-]+)(?:/?)$', $route['pattern']);
    	$this->assertEquals('topic', $route['controller']);
    	$this->assertEquals('show', $route['action']);

        // Test single requirement in array
        Router::map('/forum/{fid}', array (
            'controller' => 'forum',
            'action' => 'show',
            'requirements' => array (
                'fid' => '[0-9]+'
            ),
        ), 'show_forum');

        $route = Router::match('/forum/1234');
        $this->assertNotNull($route);
        $this->assertEquals('/forum/1234', $route['matched_url']);
    	$this->assertEquals(2, count($route['params']));
    	$this->assertEquals('^/forum/([0-9]+)(?:/?)$', $route['pattern']);

        // Test multiple requirements in array
        Router::map('/page/{pid}/{pslug}', array (
            'controller' => 'page',
            'action' => 'show',
            'requirements' => array (
                'pid' => '[0-9]+',
                'pslug' => '[a-zA-Z\-_]+'
            ),
        ), 'show_page');

        $route = Router::match('/page/1234/the-title');
    	$this->assertNotNull($route);
    	$this->assertEquals('/page/1234/the-title', $route['matched_url']);
    	$this->assertEquals(4, count($route['params']));
    	$this->assertEquals('^/page/([0-9]+)/([a-zA-Z\-_]+)(?:/?)$', $route['pattern']);
    	$this->assertEquals('page', $route['controller']);
    	$this->assertEquals('show', $route['action']);

        // Test requirement priority
        Router::map('/profile/{user:[\W]+}', array (
            'controller' => 'profile',
            'action' => 'show',
            'requirements' => array (
                'user' => '[0-9]+'
            )
        ), 'show_profile');

        $route = Router::match('/profile/mattijs hoitink');
        $this->assertEquals(false, $route);

        $route = Router::match('/profile/1234');
        $this->assertNotNull($route);
        $this->assertEquals('/profile/1234', $route['matched_url']);
    	$this->assertEquals(2, count($route['params']));
    	$this->assertEquals('^/profile/([0-9]+)(?:/?)$', $route['pattern']);
    }
    
}