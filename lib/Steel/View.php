<?php
/**
 * class Steel_View
 * @package Steel
 * @subpackage View
 *
 * @copyright 2010 Mattijs Hoitink
 * @license http://github.com/mattijs/Steel/raw/master/LICENSE Modified BSD License
 */

namespace Steel;

/**
 * View implementation for Steel.
 *
 * This class renders a view script from one of the configured paths with set 
 * variables in it's own scope.
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
     * The view script to render.
     * @var string
     */
    protected $_script = '';

    /**
     * If the view was rendered.
     * @var boolean
     */
    protected $_rendered = false;

    /** **/

    /**
     * Set the paths containing view scripts. Paths must be absolute.
     * @param array $paths
     * @return Steel_View fluent interface
     */
    public function setScriptPaths(array $paths)
    {
        $this->_scriptPaths = $paths;

        return $this;
    }

    /**
     * Add a path containing view scripts to the stack. Paths must be absolute.
     * @param string $path
     * @return Steel_View fluent interface
     */
    public function addScriptPath($alias, $path)
    {
        $this->_scriptPaths[$alias] = $path;

        return $this;
    }

    /**
     * Get the configured paths containing view scripts.
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_scriptPaths;
    }

    /**
     * Set the view script to render.
     * @param string $script
     * @return Steel_View fluent interface
     */
    public function setScript($script)
    {
        $this->_script = $script;

        return $this;
    }

    /**
     * Get the script configured for rendering.
     * @return string
     */
    public function getScript()
    {
        return $this->_script;
    }

    /**
     * Get if the script is rendered.
     * @return boolean
     */
    public function isRendered()
    {
        return $this->_rendered;
    }

    /**
     * Assign a variable to the view object.
     * @param string $key
     * @param mixed $value
     * @return Steel_View fluent interface
     */
    public function assign($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * Render the view script.
     * @return string
     */
    public function render()
    {
        // Find the view script in one of the paths
        $viewScript = $this->_script($this->getScript());

        // Catch the output
        ob_start();
        include $viewScript;
        $output = ob_get_clean();

        $this->_rendered = true;

        return $output;
    }

    /**
     * Finds a view script in one of the paths
     * @param string $name
     * @throws Steel_Exception When script could not be found
     * @return string
     */
    public function _script($name)
    {
        foreach(array_reverse($this->_scriptPaths) as $alias => $path)
        {
            $viewScript = implode(DIRECTORY_SEPARATOR, array($path, $name));
            if(is_file($viewScript)) {
                return $viewScript;
            }
        }

        // No script found
        require_once 'Steel/Exception.php';
        throw new Steel_Exception("View script {$name} could not be found. Paths: " . implode('; ', array_reverse($this->_scriptPaths)) . ";");
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function __unset ($name)
    {
        unset($this->$name);
    }
}