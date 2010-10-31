<?php
/**
 * Loader.php
 *
 * @package     Steel
 * @subpackage  Command
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/Steel/raw/master/LICENSE
 */

namespace steel\command;

/**
 * Simple loader for stack commands.
 * 
 * @package     Steel
 * @subpackage  Command
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Loader
{
    
    /**
     * Paths to look for command container classes
     * @var array
     */
    $paths = array();
    
    /** **/
    
    /**
     * Construct a new Loader
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        // Check if we need to register some paths
        if (isset($config['paths'])) {
            if (is_string($config['paths'])) {
                $this->addPath($config['paths']);
            } else {
                $this->paths = $config['paths'];
            }
        }
    }
    
    /**
     * Add a path to the loader
     * @param string $path
     * @return \steel\command\Loader
     */
    public function addPath($path)
    {
        // Convert the path to full path
        $path = realpath($path);
        
        // Make sure we only load a path once
        if (!in_array($path)) {
            $this->paths[] = $path;
        }
        
        return $this;
    }
    
    /**
     * Add multiple paths to the loader at once
     * @param array $paths
     * @return \steel\Command\Loader
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
        return $this;
    }
    
    /**
     * Load a command class. This checks the registered paths 
     * for a file with the class name.
     * @param string $commandClass
     * @return boolean TRUE on success, FALSE otherwise
     * @throws Exception when class was not found in the loaded file
     */
    public function load($commandClass)
    {
        // Check if the class was loaded before
        if (class_exists($commandClass)) {
            return true;
        }
        
        // Construct the filename
        $filename = $commandClass . '.php';
        
        // Loop the registered paths and search for the file
        foreach ($this->paths as $path) {
            $filepath = $path . DIRECTORY_SEPARATOR . $filename;
            
            // Check the path for the file containing the class
            if (file_exists($filepath)) {
                include_once $filepath;
                // Check if the requested class was loaded from the file
                if(!class_exists($commandClass)) {
                    // Class was not found in the file we loaded :(
                    throw new Exception("Class {$commandClass} was not found in file {$filepath}");
                }
                return true;
            }
        }
        
        // File was not found :(
        return false;
    }
}