<?php
/**
 * Loader.php
 * 
 * @package     Steel
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/Steel/raw/master/LICENSE
 */

namespace steel;

/**
 * Loader for Steel classes.
 *
 * @todo        add class cache
 * @package     Steel
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Loader
{

    /**
     * Autoloader implementation based on the example from
     * the PHP standards group.
     * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal
     * @param string $className
     */
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        require $fileName;
    }
    
    /**
     * Register this class with the autoloading stack.
     */
    public static function registerAutoload()
    {
        spl_autoload_register(array('self', 'autoload'));
    }
    
}