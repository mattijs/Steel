<?php
/**
 * class View
 * @package Steel
 * @subpackage View
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

namespace Steel;

/**
 * Basic view component for rendering of view scripts.
 *
 * This class renders a view script from one of the configured paths with set 
 * variables in it's own scope. The output is buffered and returned.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class View
{

    /**
     * Paths containing view scripts.
     * @var array
     */
    protected $_scriptPaths = array();

    /**
     * Variables imported into the view script scope.
     * @param array
     */
    protected $_viewVars = array();

    /** **/

    /**
     * Set the paths containing view scripts. Paths must be absolute.
     * @param array $paths
     * @return Steel\View fluent interface
     */
    public function setScriptPaths(array $paths)
    {
        $this->_ScriptPaths = array();
        foreach ($paths as $path) {
            $this->setScriptPath($path);
        }

        return $this;
    }

    /**
     * Add a path containing view scripts to the stack. Paths must be absolute.
     * @param string $path
     * @return Steel\View fluent interface
     */
    public function addScriptPath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $path = DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->_scriptPaths[] = $path;
        return $this;
    }

    /**
     * Get the configured view script paths.
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_scriptPaths;
    }

    /**
     * Assign a variable to the view object.
     * @param string $key
     * @param mixed $value
     * @return Steel\View fluent interface
     */
    public function assign($key, $value)
    {
        $this->_viewVars[$key] = $value;
        return $this;
    }
    
    /**
     * Clear the set view variables.
     * @return Steel\View fluent interface
     */
    public function clearViewVars()
    {
        $this->_viewVars = array();
        return $this;
    }

    /**
     * Render the view script.
     * @return string The script output
     */
    public function render($script)
    {
        // Find the view script in one of the paths
        $viewScript = $this->_findScript($script);
        
        // Create an internal renderer with it's own scope
        $renderer = function($script, array $vars = array()) {
            extract($vars);
            unset($vars);
            
            // Catch the output
            ob_start();
            include $script;
            $output = ob_get_clean();
            
            return $output;
        };
        
        return $renderer($viewScript, $this->_viewVars);
    }

    /**
     * Find a view script in one of the paths. The paths are scanned in reverse 
     * order. The first match is returned.
     * @param string $name
     * @throws Steel\Exception When no script paths are configured
     * @throws Steel\Exception\NotFound When view script could not be found
     * @return string the script path
     */
    public function _findScript($name)
    {
        if (empty($this->_scriptPaths)) {
            throw new \Steel\Exception('No script paths configured');
        }
        
        foreach(array_reverse($this->_scriptPaths) as $path)
        {
            $viewScript = implode(DIRECTORY_SEPARATOR, array($path, $name));
            if(is_file($viewScript)) {
                return $viewScript;
            }
        }

        // No script found
        throw new \Steel\Exception\NotFound("View script {$name} could not be found. Paths: " . implode('; ', array_reverse($this->_scriptPaths)) . ";");
    }

    public function __get($name)
    {
        if (isset($name, $this->_viewVars)) {
            return $this->_viewVars[$name];
        }
    }

    public function __set($name, $value)
    {
        $this->_viewVars[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_viewVars[$name]);
    }

    public function __unset ($name)
    {
        if (isset($this->_viewVars[$name])) {
            unset($this->_viewVars[$name]);
        }
    }
}