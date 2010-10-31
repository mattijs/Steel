<?php
/**
 * Url.php
 *
 * @package     Steel
 * @subpackage  Net
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/steel/raw/master/LICENSE
 */

namespace steel\net\http;

/**
 * Class for working with Unified Resource Locations or URL's. 
 * A new instance can be created using the constructor and an 
 * array or by calling the static method parse and providing 
 * a string.
 *
 * @package     Steel
 * @subpackage  Net
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Url
{
    /**
     * Scheme to reach the resource
     * @var string
     */
    public $scheme = 'http';

    /**
     * Authorisation required to reach the resource.
     * @var array
     */
    public $auth = array();

    /**
     * Hostname the resource is located on.
     * @var string
     */
    public $host = 'localhost';

    /**
     * Port the resource can be reached on
     * @var int|string
     */
    public $port = 80;

    /**
     * Path to the resource on the host.
     * @var string
     */
    public $path = '/';

    /**
     * Query string parameters for the resource.
     * @var array
     */
    public $query = array();

    /**
     * Fragment part for the resource.
     * @var string
     */
    public $fragment = '';

    /** **/

    /**
     * Construct a new URL
     * @param array $config Configuration to build the URL
     */
    public function __construct(array $config = array())
    {
        $defaults = array (
            'scheme' => 'http',
            'auth' => array(),
            'host' => 'localhost',
            'port' => 80,
            'path' => '/',
            'query' => array(),
            'fragment' => ''
        );
        $config += $defaults;

        // Copy configruation to public members
        foreach($config as $key => $value) {
            $this->{$key} = $value;
        }

        // Correct the path
        $this->path = '/' . ltrim($this->path, '/');
    }

    /**
     * Set a part of the query string and/or get the build query string.
     * 
     * @param array $params The parameters to add to the query string
     * @return string The build query string
     */
    public function queryString(array $params = array())
    {
        if (!empty($params)) {
            $this->query = array_merge($this->query, $params);
        }
        
        if (empty($this->query)) {
            return '';
        }
        
        return http_build_query($this->query);
    }

    /**
     * Build the URL to a string from its individual parts.
     * @return string The build URL
     */
    public function build()
    {
        $scheme = $this->scheme . '://';
        $auth = implode(':', $this->auth);
        $auth = !empty($auth) ? trim($auth, ':') . '@' : '';
        $host = ltrim($this->host, '/');
        $port = ($this->port > 0) ? ':' . $this->port : '';
        $path = '/' . rtrim($this->path, '/');
        $query = ('' !== $this->queryString()) ? '?' . $this->queryString() : '';
        $fragment = (!empty($this->fragment)) ? '#' . $this->fragment : '';
        
        // Construct the url from the variables defined in this methods scope
        return implode('', get_defined_vars());
    }

    /**
     * Convert the `Url` to a specified type. Supported types:
     * - array
     * - string
     * @param string $type
     * @return mixed
     */
    public function to($type)
    {
        switch(strtolower($type)) {
            case 'array':
                return get_object_vars($this);
                break;
            case 'string':
            default:
                return (string) $this;
                break;
        }
    }

    /**
     * Returns a string representation of the URL
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
    
    /**
     * Parses a string into an `Url` class.
     * @param string $url The URL string to parse
     * @return \wowp\http\Url A decompiled `Url` class
     */
    public static function parse($url)
    {
        $parts = parse_url($url);
        if (false === $parts) {
            throw new Exception('Passed URL could not be parsed. It may be malformed.');
        }
        
        // Parse query string as an array
        $parts['query'] = call_user_func(function($query) {
            parse_str($query);
            unset($query);
            return get_defined_vars();
        }, $parts['query']);
        
        // Strip auth parts and replace them as an array
        $auth = array();
        if (isset($parts['user'])) {
            $auth['username'] = $parts['user'];
            unset($parts['user']);
        }
        if (isset($parts['pass'])) {
            $auth['password'] = $parts['pass'];
            unset($parts['pass']);
        }
        $parts['auth'] = $auth;

        return new self($parts);
    }
}
