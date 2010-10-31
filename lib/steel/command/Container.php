<?php
/**
 * Container.php
 * 
 * @package     Steel
 * @subpackage  Command
 * @copyright   Copyright (c) 2010 Mattijs Hoitink <mattijs@monkeyandmachine.com>
 * @license     New BSD License - http://github.com/mattijs/Steel/raw/master/LICENSE
 */

namespace steel\command;

/**
 * Abstract container class for housing command logic. 
 * This class can be used to save logic to class files 
 * and load them into the Sequence class either by passing 
 * them to the register function or by using the 
 * \steel\Command\Loader class.
 * 
 * @package     Steel
 * @subpackage  Command
 * @author      Mattijs Hoitink <mattijs@monnkeyandmachine.com>
 */
abstract class Container 
{
    /**
     * Execute the command
     * @param \steel\command\Sequence $sequence
     * @return mixed
     */
    abstract function execute(\steel\command\Sequence $sequence);
}