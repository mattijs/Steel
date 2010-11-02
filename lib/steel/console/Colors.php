<?php
/**
 * Colors.php
 * 
 * @package     Steel
 * @subpackage  Console
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/Steel/raw/master/LICENSE
 */

namespace steel\console;

/**
 * Class for adding ANSII colors to text. This class supports 
 * a formatting style as described with the format() method.
 * 
 * @package     Steel
 * @subpackage  Console
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Colors
{
    /**
     * Terminal colors
     * @var array
     */
    public static $colors = array (
        'foreground' => array (
            'black'      => 30,
            'red'        => 31,
            'green'      => 32,
            'yellow'     => 33,
            'blue'       => 34,
            'magenta'    => 35,
            'cyan'       => 36,
            'white'      => 37
        ),
        'background' => array (
            'black'      => 40,
            'red'        => 41,
            'green'      => 42,
            'yellow'     => 43,
            'blue'       => 44,
            'magenta'    => 45,
            'cyan'       => 46,
            'white'      => 47
        ),
        'modifier' => array (
            'normal'     => 0,
            'bright'     => 1,
            'bold'       => 1,
            'dim'        => 2,
            'underscore' => 4,
            'underline'  => 4,
            'blink'      => 5,
            'inverse'    => 6,
            'hidden'     => 8,
        )
    );
    
    /**
     * The sescape character for colors
     * @var string
     */
    public static $escape = "\033";
    
    /**
     * Reset string
     * @var string
     */
    public static $reset = "\033[0m";
    
    /** **/
    
    /**
     * Get the ANSII color code for the color definition.
     * The color can be passed as a string or an array. If 
     * a string is passed it is assumed the foreground color 
     * must be set.
     * If an array is passed the foreground, background and 
     * modifier can be set.
     * @param string|array $color
     */
    public static function get($color)
    {
        if (is_string($color) && 'reset' === strtolower($color)) {
            return self::reset();
        }
        else if (is_string($color)) {
            $color = array('foreground' => $color);
        }
        
        // Update color code with sensible defaults
        $color += array('foreground' => 'black', 'background' => null, 'modifier' => null);
        
        // Construct the color code
        $code = array();
        foreach (array_keys($color) as $type) {
            $colorCode = self::code($color[$type], $type);
            if (null !== $colorCode) {
                $code[] = $colorCode;
            }
        }
        
        return self::$escape . '[' . implode(';', $code) . 'm';
    }
    
    /**
     * Returns the ANSII reset code
     * @return string
     */
    public function reset()
    {
        return self::$reset;
    }
    
    /**
     * Returns the numeric ANSII code for a color
     * @param string $key
     * @param string $type
     * @return int
     */
    public static function code($key, $type = 'foreground')
    {
        if (!array_key_exists($type, self::$colors)
            || !array_key_exists($key, self::$colors[$type])
        ) {
            return null;
        }
        
        return self::$colors[$type][$key];
    }
    
    /**
     * Format a message with colors.
     * The formatting rules are copied from the Console_Colors 
     * package on Pear.
     * <pre> 
     *                  text      text            background
     *      ------------------------------------------------
     *      %k %K %0    black     dark grey       black
     *      %r %R %1    red       bold red        red
     *      %g %G %2    green     bold green      green
     *      %y %Y %3    yellow    bold yellow     yellow
     *      %b %B %4    blue      bold blue       blue
     *      %m %M %5    magenta   bold magenta    magenta
     *      %p %P       magenta (think: purple)
     *      %c %C %6    cyan      bold cyan       cyan
     *      %w %W %7    white     bold white      white
     *
     *      %F     Blinking, Flashing
     *      %U     Underline
     *      %8     Reverse
     *      %_,%9  Bold
     *
     *      %n     Resets the color
     *      %%     A single %
     * </pre>
     * @see http://pear.php.net/package/Console_Color
     * @param string $message
     * @return string
     */
    public static function format($message)
    {
        // Conversion rules
        static $conversions = array (
            '%y' => array('foreground' => 'yellow'),
            '%g' => array('foreground' => 'green' ),
            '%b' => array('foreground' => 'blue'  ),
            '%r' => array('foreground' => 'red'   ),
            '%p' => array('foreground' => 'magenta'),
            '%m' => array('foreground' => 'magenta'),
            '%c' => array('foreground' => 'cyan'  ),
            '%w' => array('foreground' => 'grey'  ),
            '%k' => array('foreground' => 'black' ),
            '%n' => 'reset',
            '%Y' => array('foreground' => 'yellow',  'modifier' => 'bold'),
            '%G' => array('foreground' => 'green',   'modifier' => 'bold'),
            '%B' => array('foreground' => 'blue',    'modifier' => 'bold'),
            '%R' => array('foreground' => 'red',     'modifier' => 'bold'),
            '%P' => array('foreground' => 'magenta', 'modifier' => 'bold'),
            '%M' => array('foreground' => 'magenta', 'modifier' => 'bold'),
            '%C' => array('foreground' => 'cyan',    'modifier' => 'bold'),
            '%W' => array('foreground' => 'grey',    'modifier' => 'bold'),
            '%K' => array('foreground' => 'black',   'modifier' => 'bold'),
            '%N' => 'reset',
            '%0' => array('background' => 'black' ),
            '%1' => array('background' => 'red'   ),
            '%2' => array('background' => 'green' ),
            '%3' => array('background' => 'yellow'),
            '%4' => array('background' => 'blue'  ),
            '%5' => array('background' => 'magenta'),
            '%6' => array('background' => 'cyan'  ),
            '%7' => array('background' => 'grey'  ),
            '%F' => array('modifier'   => 'blink'),
            '%U' => array('modifier'   => 'underline'),
            '%8' => array('modifier'   => 'inverse'),
            '%9' => array('modifier'   => 'bold'),
            '%_' => array('modifier'   => 'bold')
        );
        
        // Add a space to double percent signs, 
        // this will be converted to a single percent sign later
        $message = str_replace('%%', '% ', $message);
        
        // Convert all rules in the message to color codes
        foreach ($conversions as $key => $color) {
            $message = str_replace($key, self::get($color), $message);
        }
        
        // Reset single percent signs
        $message = str_replace('% ', '%', $message);
        
        // Return formatted message
        return $message;
    }
}