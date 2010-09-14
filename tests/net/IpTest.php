<?php
/**
 * class IpTest
 * @package Steel
 * @category Tests
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

require_once 'PHPUnit/Framework.php';

use steel\core\Loader as Loader;
use steel\net\Ip as Ip;

/**
 * Unit test for steel\net\Ip class.
 * 
 * @author Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class IpTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        set_include_path(realpath(__DIR__ . '/../../lib') . PATH_SEPARATOR . get_include_path());

        // Register Steel autloader
        require_once 'steel/core/Loader.php';
        Loader::registerAutoload();
    }

    public function testEqualsCheck()
    {
        $this->assertEquals(false, Ip::equals('192.168.1.2', '192.168.2.1'));
        $this->assertEquals(true, Ip::equals('192.168.1.2', '192.168.1.2'));
    }

    
    /**
     * Test all possible subnets and netmasks
     */
    public function testSubnetCheck()
    {
        // Check with netmask
        $this->assertEquals(false, Ip::inSubnet('192.168.2.1', '192.168.1.0', '24'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.2', '192.168.1.0', '24'));

        // Netmask 24 - Subnet 255.255.255.0
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '24'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '24'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.253', '192.168.1.0', '24'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.254', '192.168.1.0', '24'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.255', '192.168.1.0', '24'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.256', '192.168.1.0', '24'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.0'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.0'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.253', '192.168.1.0', '255.255.255.0'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.254', '192.168.1.0', '255.255.255.0'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.255', '192.168.1.0', '255.255.255.0'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.256', '192.168.1.0', '255.255.255.0'));

        // Netmask 25 - Subnet 255.255.255.128
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '25'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '25'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.125', '192.168.1.0', '25'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.126', '192.168.1.0', '25'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.127', '192.168.1.0', '25'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.150', '192.168.1.0', '25'));
        /** ~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.128'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.128'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.125', '192.168.1.0', '255.255.255.128'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.126', '192.168.1.0', '255.255.255.128'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.127', '192.168.1.0', '255.255.255.128'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.150', '192.168.1.0', '255.255.255.128'));

        // Netmask 26 - Subnet 255.255.255.192
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '26'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '26'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.61', '192.168.1.0', '26'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.62', '192.168.1.0', '26'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.63', '192.168.1.0', '26'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '26'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.192'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.192'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.61', '192.168.1.0', '255.255.255.192'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.62', '192.168.1.0', '255.255.255.192'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.63', '192.168.1.0', '255.255.255.192'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '255.255.255.192'));

        // Netmask 27 - Subnet 255.255.255.224
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '27'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '27'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.29', '192.168.1.0', '27'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.30', '192.168.1.0', '27'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.31', '192.168.1.0', '27'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '27'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.224'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.224'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.29', '192.168.1.0', '255.255.255.224'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.30', '192.168.1.0', '255.255.255.224'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.31', '192.168.1.0', '255.255.255.224'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '255.255.255.224'));

        // Netmask 28 - Subnet 255.255.255.240
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '28'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '28'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.13', '192.168.1.0', '28'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.14', '192.168.1.0', '28'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.15', '192.168.1.0', '28'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '28'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.240'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.240'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.13', '192.168.1.0', '255.255.255.240'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.14', '192.168.1.0', '255.255.255.240'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.15', '192.168.1.0', '255.255.255.240'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.100', '192.168.1.0', '255.255.255.240'));

        // Netmask 29 - Subnet 255.255.255.248
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '29'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '29'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.5', '192.168.1.0', '29'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.6', '192.168.1.0', '29'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.7', '192.168.1.0', '29'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.50', '192.168.1.0', '29'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.248'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.248'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.5', '192.168.1.0', '255.255.255.248'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.6', '192.168.1.0', '255.255.255.248'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.7', '192.168.1.0', '255.255.255.248'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.50', '192.168.1.0', '255.255.255.248'));

        // Netmask 30 - Subnet 255.255.255.252
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '30'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '30'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.2', '192.168.1.0', '30'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.3', '192.168.1.0', '30'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.25', '192.168.1.0', '30'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.252'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.252'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.2', '192.168.1.0', '255.255.255.252'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.3', '192.168.1.0', '255.255.255.252'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.25', '192.168.1.0', '255.255.255.252'));

        // Netmask 31 - Subnet 255.255.255.254
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '31'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '31'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.2', '192.168.1.0', '31'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.3', '192.168.1.0', '31'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.50', '192.168.1.0', '31'));
        /** ~~ **/
        $this->assertEquals(true, Ip::inSubnet('192.168.1.0', '192.168.1.0', '255.255.255.254'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.1', '192.168.1.0', '255.255.255.254'));
        $this->assertEquals(true, Ip::inSubnet('192.168.1.2', '192.168.1.0', '255.255.255.254'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.3', '192.168.1.0', '255.255.255.254'));
        $this->assertEquals(false, Ip::inSubnet('192.168.1.50', '192.168.1.0', '255.255.255.254'));

    }
    
    public function testRangeCheck()
    {


    }

}
