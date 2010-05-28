<?php
/**
 * class Autoloader
 * @package Steel
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
 * Autoloader for Steel classes
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Autoloader
{

    /**
     * Autoload function. Tries to resolve a classname by converting it to a
     * file path and including it. The include path is scanned for the generated
     * file path.
     * @param string $className
     * @return void
     */
    public static function autoload($className)
    {
        $pathPrefix = '';
        if(0 !== strpos($className, 'Steel\\')) {
            $pathPrefix = 'Steel' . DIRECTORY_SEPARATOR;
        }
        
        $classFile = $pathPrefix . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        include $classFile;
    }
    
    /**
     * Register this class with the autoloading stack.
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array('self', 'autoload'));
    }
    
}