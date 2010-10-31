<?php
/**
 * Stack.php
 * 
 * @package     Steel
 * @subpackage  Command
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/Steel/raw/master/LICENSE
 */

namespace steel\command;

/**
 * Sequence of commands that can be executed. This class is based 
 * on SplQueue.
 * Commands can be added to the sequence as anonimous functions 
 * or as \steel\command\Container classes. The registered commands 
 * will be executed in sequence when the execute method is called.
 * 
 * @package     Steel
 * @subpackage  Command
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Sequence extends \SplQueue
{
    /**
     * Loader for command Container classes
     * @var \steel\command\Loader
     */
    protected $loader = null;
    
    /**
     * Configuration for the stack
     * @var array
     */
    protected $config = array();
    
    /**
     * Results from each command
     * @param array
     */
    private $results = array();
    
    /** **/
    
    /**
     * Construct a new Sequence
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }
    
    /**
     * Returns the config or a config option from 
     * the Sequence
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public function config($option = null, $default = null)
    {
        if (null === $option) {
            return $this->config;
        }
        else if (array_key_exists($option, $this->config)) {
            return $this->config[$option];
        }
        
        return $default;
    }
    
    /**
     * Register a command in the sequence
     * @param \Closure|\steel\command\Container $command
     * @return Sequence
     */
    public function register($command)
    {
        // Check if the command is an anonymous function or a Container class.
        if (!$command instanceof \Closure 
            && !$command instanceof \steel\command\Container) 
        {
            throw new Exception('Command must be an anonymous function or an instance of \steel\command\Container');
        }
        
        $this->push($command);
        return $this;
    }
    
    /**
     * Clear the commands in the sequence
     * @return \steel\command\Stack
     */
    public function clear()
    {
        do {
            $this->shift();
        } while(!$this->isEmpty());
        
        return $this;
    }
    
    /**
     * Execute the commands in the sequence
     */
    public function execute()
    {
        // Reset the sequence
        $this->rewind();
        
        // Loop the commands
        foreach ($this as $key => $command) {
            if ($command instanceof \Closure) {
                $result = $command($this);
            } 
            else {
                $result = $command->execute($this);
            }
            $this->results[$key] = $result;
        }
        
        return true;
    }
}