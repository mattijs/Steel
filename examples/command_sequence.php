<?php
/**
 * command_sequence.php
 * 
 * Example of useing the command sequence with 
 * anonymous functions and with the command Container.
 * 
 * @package     Steel
 * @category    examples
 * @author      Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */

// Add Steel library to the include path
set_include_path(realpath(__DIR__ . '/../lib') . PATH_SEPARATOR . get_include_path());

// Register the Steel autoloader
require 'steel/Loader.php';
\steel\Loader::registerAutoload();

// Create a new sequence
$sequence = new \steel\command\Sequence();

// Add a two commands as anonymous functions
// These anonymous functions are passed the instance
// of the Sequence class when they are executed
$sequence->register(function($sequence) {
    echo 'This command is executed as an anonymous funcion.' . "\n";
});

$sequence->register(function($sequence) {
    echo 'The anonymous functions can access the sequence object as it is passed into the function.' . "\n";
});

// Execute the command sequence
$sequence->execute();

// Add a command using the Container class
class SimpleCommand extends \steel\command\Container
{
    public function execute(\steel\command\Sequence $sequence) 
    {
        echo "This command is executed from a Container\n";
    }
}

// Clear the sequence first to remove the anonymous functions
$sequence->clear();

// Load our freshly created class
$sequence->register(new SimpleCommand());

// Execute the sequence
$sequence->execute();
