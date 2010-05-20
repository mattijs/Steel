<?php
/**
 * class Autoloader
 * @package Steel
 * @copyright 2010 Mattijs Hoitink
 * @license http://github.com/mattijs/Steel/raw/master/LICENSE Modified BSD License
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
    
}