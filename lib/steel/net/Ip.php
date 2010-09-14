<?php

namespace steel\net;

/**
 * Utility class for working with IP addresses.
 *
 * @author Mattijs Hoitink <mattijs@monkeyandmachine.com>
 */
class Ip
{
    /**
     * Compare an IP angainst a set of range rules.
     * @param string $ip The IP to compare
     * @param array|string $ranges The range rules.
     */
    public static function inRange($ip, $ranges)
    {
        if (is_string($ranges)) {
            $ranges = array($ranges);
        }
        else if (!is_array($ranges)) {
            throw new \Exception('Range definitions must be an array or a string.');
        }

        foreach ($ranges as $singleOrRange) {
            if (false !== strpos('/', $singleOrRange)) {
                list($rangeStart, $netmask) = explode('/', $singleOrRange);
                self::inNetmaskRankge($ip, $rangeStart, $netmask);
            }
            else {
                self::equals($ip, $singleOrRange);
            }
        }
    }

    /**
     * Check if two IP addresses are equal.
     * @param string $ip The IP to check
     * @param string $check The IP to check against
     * @return boolean TRUE if the IP addresses are equal, FALSE otherwise
     */
    public static function equals($ip, $check)
    {
        return (boolean) (self::toLong($ip) === self::toLong($check));
    }
    
    /**
     * Check if an IP address is within a netmask range.
     * @param string $ip The IP address to verify
     * @param string $start The first IP address in the subnet
     * @param string $netmaskOrSubnet The netmask or full subnet
     * @return boolean TRUE if the IP is within the subnet, FALSE otherwise
     */
    public static function inSubnet($ip, $start, $netmaskOrSubnet)
    {
        // Calculate the number of available hosts in the subnet
        if (false !== \strpos($netmaskOrSubnet, '.')) {
            $subnetSections = explode('.', $netmaskOrSubnet);
            if (0 >= count($subnetSections) || 4 < count($subnetSections)) {
                throw new \Exception('Subnet must consist of 4 sections.');
            }
            $availableHosts = 254 - array_pop($subnetSections);

            // Check for special point-to-point case
            if (0 == $availableHosts) {
                $availableHosts = 2;
            }
        }
        else if (2 === \strlen((string) $netmaskOrSubnet) && (23 < $netmaskOrSubnet && $netmaskOrSubnet < 32)) {
            $hostPortionBits = 32 - $netmaskOrSubnet;
            $availableHosts =  pow(2, $hostPortionBits) - 2;

            // Check for special point-to-point case
            if (0 == $availableHosts && 31 == $netmaskOrSubnet) {
                $availableHosts = 2;
            }
        }
        else {
            throw new \Exception("Invalid netmask or subnet: \"{$netmaskOrSubnet}\"");
        }

        // Break the minimum IP into sections
        $ipSections = explode('.', $start);

        // Reconstruct the two IP's
        $ipMin = implode('.', $ipSections);
        $ipMinLong = static::toLong($ipMin);
        $ipSections[3] += $availableHosts;
        $ipMax = implode('.', $ipSections);
        $ipMaxLong = static::toLong($ipMax);

        // Check if the IP is within the range
        $ipLong = self::toLong($ip);
        return (boolean) ($ipLong >= $ipMinLong && $ipLong <= $ipMaxLong);
    }

    /**
     * Check if an IP address is valid. 
     * This function uses ip2long() and checks if the return value is not FALSE.
     * @param string $ip The IP address to check
     * @return boolean TRUE if the IP address is valid, FALSE otherwise
     */
    public static function isValid($ip)
    {
        return (boolean) (false !== ip2long($ip));
    }

    /**
     * Convert an IP to a long in a safe way, compatible with 
     * 32 bit and 64 bit machines.
     * @param string $ip The IP to convert
     * @return long The long number representing the IP
     */
    public static function toLong($ip)
    {
        return array_pop(unpack('l', pack('l', ip2long($ip))));
    }
}
