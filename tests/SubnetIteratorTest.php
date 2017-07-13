<?php

namespace ColinODell\Ipv4\Test;

use ColinODell\Ipv4\Subnet;
use ColinODell\Ipv4\SubnetIterator;

class SubnetIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testManualIteration()
    {
        $iterator = new SubnetIterator(new Subnet('192.168.1.0/24'));

        $this->assertEquals('192.168.1.1', $iterator->current());

        $iterator->next();
        $this->assertEquals('192.168.1.2', $iterator->current());

        $iterator->next();
        $this->assertEquals('192.168.1.3', $iterator->current());
    }

    public function testForEachIteration()
    {
        $iterator = new SubnetIterator(new Subnet('192.168.1.0/24'));

        $ips = array();
        foreach ($iterator as $k => $ip) {
            $ips[] = $ip;
        }

        $this->assertCount(254, $ips);
        $this->assertEquals('192.168.1.1', reset($ips));
        $this->assertEquals('192.168.1.254', end($ips));
    }

    public function testRewind()
    {
        $iterator = new SubnetIterator(new Subnet('192.168.1.0/24'));

        $firstIp = $iterator->current();
        while ($iterator->valid()) {
            $iterator->next();
        }

        $this->assertNotEquals($firstIp, $iterator->current());

        $iterator->rewind();
        $this->assertEquals($firstIp, $iterator->current());
    }
}
