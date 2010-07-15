<?php
/**
 * class Router
 *
 * @package Steel
 * @subpackage Router
 * @namespace steel\net\http
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

namespace steel\net\http;

/**
 * Steel Router class takes care of mapping urls to a controller and action.
 * Route definitions support named parameters and regular expressions.
 *
 * @todo Add more documentation on route definitions
 * @todo Implement request method checking
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Router
{

    /**
     * Map with defined routes.
     * @var array
     */
    protected static $_map = array();

    /**
     * Map an url to a controller and action. The second parameter specifies
     * the options for mapping the url. Possible options are:
     * <ul>
     *   <li>controller</li>
     *   <li>action</li>
     *   <li>methods</li>
     * </ul>
     * @param string $pattern The URL pattern to map
     * @param array $options The options for the map
     * @param string $name An optional name for the map
     */
    public static function map($pattern, array $options, $name = '')
    {
        $defaults = array(
            'controller' => 'default',
            'action' => 'default',
            'requirements' => array(),
            'params' => array(),
            'methods' => array(),
        );
        $route = $options + $defaults;
        $route['pattern'] = trim($pattern);

        // Build the route
        $route = self::build($route);
        
        if (!empty($name)) {
            self::$_map[trim($name)] = $route;
        } else {
            self::$_map[] = $route;
        }
    }

    /**
     * Build the route and add it to the map. This method extracts named
     * parameters and regular expressions.
     * @param array $route
     * @return void
     */
    public static function build(array $route)
    {
        $parts = preg_split('~[/]+~', $route['pattern'], -1, PREG_SPLIT_NO_EMPTY);

        $paramMap = array();
        foreach ($parts as $index => &$part) {
            // Catch named parameters
            if (1 === preg_match('/^\{(.*)\}$/i', $part, $matches)) {
				list($name, $requirements) = explode(':', array_pop($matches), 2) + array('', '');

                // Use possible requirement as route pattern
                if (!empty($requirements)) {
                    // Inline requirements don't go above explicitly defined requirements
                    $route['requirements'] = $route['requirements'] + array($name => $requirements);
                }

                // Get and sanitize the pattern to replace the name part
                $pattern = isset($route['requirements'][$name]) ? $route['requirements'][$name] : '([^/]+)' ;
                $part = '(' . trim($pattern, '^()$') . ')';
				
                // Store the name of the parameter
				$paramMap[$index] = $name;
            }
        }
		
		// Update the complete route pattern
        $route['pattern'] = '^/' . implode('/', $parts) . '(?:/?)$';
		// Store the parameter map for internal use
        $route['_paramMap'] = $paramMap;

        return $route;
    }

    /**
     * Try to match an URL to a route. When a match is found the route is
     * returned. If no route matched false is returned.
     * @param string $url
     * @return array|false
     */
    public static function match($url)
    {
        $routes = self::$_map;

        foreach ($routes as $route) {
            $pattern = str_replace('~', '\~', $route['pattern']);
            if (1 === preg_match("~$pattern~i", $url, $paramMatches)) {
                
                // First match item contains the complete matched url
                $route['matched_url'] = $url = array_shift($paramMatches);

                // Shift first item, it's always empty because of leading /
                $urlParts = explode('/', $url);
                array_shift($urlParts);
                ksort($urlParts);

                // Get the parameter map and reuse the params array for 
                // storing the actual parameters
                $paramMap = $route['_paramMap'];
                ksort($paramMap);
                
                // If a parameter mapping is present, add those to the parameters array
                if (!empty($paramMap)) {
                    // Always add parameters by their numeric key
                    $route['params'] += array_intersect_key($urlParts, $paramMap);
                    
                    // If the parameter map contains names for the matches, 
                    // add the matches by their names as well
                    $route['params'] += array_combine($paramMap, array_intersect_key($urlParts, $paramMap));
                }
                
                return $route;
            }
        }

        return false;
    }

    /**
     * Get the configured routes
     * @return array
     */
    public static function getRoutes()
    {
        return self::$_map;
    }
    
    /**
     * Get a route by name.
     * @param string $name
     * @throws Steel\Exception When route is not found
     * @return array
     */
    public static function getRoute($name)
    {
        $name = trim($name);
        if (array_key_exists($name, self::$_map)) {
            return self::$_map[$name];
        }
        
        throw new \Steel\Exception\NotFound("Route with name {$name} not found.");
    }

    /**
     * Clear the mapped routes.
     * @return void
     */
    public static function clear()
    {
        self::$_map = array();
    }

}
