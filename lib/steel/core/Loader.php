<?php
/**
 * class Loader
 * @package Steel
 * @subpackage Core
 * @namespace steel\core
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

namespace steel\core;

/**
 * Loader for Steel classes. Can perform autoloading. 
 *
 * @todo add class cache
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Loader
{

    /**
     * Autoloader implementation based on the example from
     * the php standards group.
     * {@see http://groups.google.com/group/php-standards/web/psr-0-final-proposal}
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
     * @return void
     */
    public static function registerAutoload()
    {
        spl_autoload_register(array('self', 'autoload'));
    }
    
}