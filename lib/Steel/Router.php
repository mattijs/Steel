<?php
/**
 * class Router
 *
 * @package Steel
 * @subpackage Router
 *
 * @copyright 2010 Mattijs Hoitink
 * @license http://github.com/mattijs/Steel/raw/master/LICENSE Modified BSD License
 */

namespace Steel;

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
     * Map an url to a controller and actions. The second parameter specifies
     * the options for mapping the url. Possible options are:
     * <ul>
     *   <li>controller</li>
     *   <li>action</li>
     *   <li>methods</li>
     * </ul>
     * @param string $pattern
     * @param array $options
     */
    public static function map($pattern, array $options)
    {
        $defaults = array(
            'controller' => 'default',
            'action' => 'default',
            'params' => array(),
        );
        $route = $options + $defaults;

        $route['pattern'] = trim($pattern);

        self::build($route);
    }

    /**
     * Build the route and add it to the map. This method extracts named
     * parameters and regular expressions.
     * @param array $route
     * @return void
     */
    protected static function build(array $route)
    {
        $parts = explode('/', $route['pattern']);

        $params = array();
        foreach ($parts as $index => &$part) {
            if (array_key_exists($index, $route['params'])) {
                continue;
            }

            // Catch named parameters
            if (1 === preg_match('/<([^>]*)>/i', $part, $matches)) {
                // Decrease index. The first part is always empty because of the
                // leading slash (/)
                $params[--$index] = array_pop($matches);
                // Replace the part with a regular expression
                $part = '([^\/]+)';
            }
            // Catch regular expressions
            /**
             * This wont pass when curly brackets ({, }) are used within the
             * regex we are trying to match.
             * @todo improve regex detection
             */
            else if (1 === preg_match('/{([^}]*)}/i', $part, $matches)) {
                $part = array_pop($matches);
            }
        }
        

        $route['pattern'] = implode('/', $parts) . '(?:/?)';
        $route['params'] = $params + $route['params'];

        self::$_map[] = $route;
    }

    /**
     * Get teh configured routes
     * @return array
     */
    public static function getRoutes()
    {
        return self::$_map;
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
            $pattern = str_replace(';', '\;', $route['pattern']);
            if (1 === preg_match(";{$pattern};i", $url, $matches)) {
                $route['matched_url'] = $url;

                $parts = explode('/', $url);
                // Shift first item, it's always empty because of leading /
                array_shift($parts);

                /*
                 * Refill the parameters with the actual values extracted from
                 * the url. Parameters are also added on the position they were
                 * found in the url.
                 */
                $route['params'] = array_combine(
                    $route['params'],
                    array_intersect_key($parts, $route['params'])
                ) + array_intersect_key($parts, $route['params']);

                return $route;
            }
        }

        return false;
    }

}