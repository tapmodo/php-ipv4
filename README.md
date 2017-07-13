# IPv4 classes for PHP

[![Latest Version](https://img.shields.io/packagist/v/colinodell/ipv4.svg?style=flat-square)](https://packagist.org/packages/colinodell/ipv4)
[![Total Downloads](https://img.shields.io/packagist/dt/colinodell/ip4v.svg?style=flat-square)](https://packagist.org/packages/colinodell/ipv4)
[![Software License](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/colinodell/php-ipv4/master.svg?style=flat-square)](https://travis-ci.org/colinodell/php-ipv4)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/colinodell/php-ipv4.svg?style=flat-square)](https://scrutinizer-ci.com/g/colinodell/php-ipv4/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/colinodell/php-ipv4.svg?style=flat-square)](https://scrutinizer-ci.com/g/colinodell/php-ipv4)

### Purpose

Identify, convert, and enumerate IPv4 IP addresses and subnets

### Installation

composer require colinodell/ipv4:dev-master

### Examples

    use ColinODell\Ipv4\Address;
    use ColinODell\Ipv4\Subnet;

    $ip = Address::fromString('10.2.1.1');
    $sn = Subnet::fromString('10.2.0.0/16');

    // Test if IP is in subnet
    $sn->contains($ip)          // true
    $sn->contains('10.3.1.23')  // false
    Subnet::containsAddress($sn,$ip)
    Subnet::containsAddress('192.168.1.0/27','192.168.1.246')

    // Test if two IPs are on the same network
    $netmask = '255.255.255.0';
    Subnet::containsAddress(new Subnet($ip1,$netmask),$ip2)

    // Can be written in numerous ways...
    Subnet::containsAddress("{$ip1}/24",$ip2)
    Subnet::fromString("{$ip1}/24")->contains($ip2)

    // Subnet information
    $sn->getNetwork()
    $sn->getNetmask()
    $sn->getNetmaskCidr()
    $sn->getFirstHostAddr()
    $sn->getLastHostAddr()
    $sn->getBroadcastAddr()

    // Enumerate subnet addresses
    foreach($sn as $addr) ...

    // Count number of usable IPs on subnet (implements Countable)
    $sn->getTotalHosts()
    $sn->count()
    count($sn)
