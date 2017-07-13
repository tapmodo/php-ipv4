<?php

namespace ColinODell\Ipv4\Test;

use ColinODell\Ipv4\Subnet;

class SubnetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \ColinODell\Ipv4\Subnet::__construct()
     * @covers \ColinODell\Ipv4\Subnet::getNetwork()
     * @covers \ColinODell\Ipv4\Subnet::getNetmaskCidr()
     * @covers \ColinODell\Ipv4\Subnet::getNetmask()
     * @covers \ColinODell\Ipv4\Subnet::getFirstHostAddr()
     * @covers \ColinODell\Ipv4\Subnet::getLastHostAddr()
     * @covers \ColinODell\Ipv4\Subnet::getBroadcastAddr()
     * @covers \ColinODell\Ipv4\Subnet::getTotalHosts()
     * @covers \ColinODell\Ipv4\Subnet::count()
     * @covers \ColinODell\Ipv4\Subnet::__toString()
     * @covers \ColinODell\Ipv4\Subnet::equals()
     */
    public function testConstructorGettersAndEquals()
    {
        $subnet1 = new Subnet('192.168.1.0/24');
        $subnet2 = new Subnet('192.168.1.0', '255.255.255.0');

        foreach (array($subnet1, $subnet2) as $subnet) {
            $this->assertEquals('192.168.1.0', $subnet->getNetwork());
            $this->assertEquals('24', $subnet->getNetmaskCidr());
            $this->assertEquals('255.255.255.0', $subnet->getNetmask());
            $this->assertEquals('192.168.1.1', $subnet->getFirstHostAddr());
            $this->assertEquals('192.168.1.254', $subnet->getLastHostAddr());
            $this->assertEquals('192.168.1.255', $subnet->getBroadcastAddr());
            $this->assertEquals(254, $subnet->getTotalHosts());
            $this->assertEquals(254, $subnet->count());
            $this->assertCount(254, $subnet);
            $this->assertEquals('192.168.1.0/24', $subnet->__toString());
        }

        $this->assertTrue($subnet1->equals($subnet2));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::cidrToIp()
     */
    public function testCidrToIp()
    {
        $this->assertEquals('255.192.0.0', Subnet::cidrToIp(10));
        $this->assertEquals('255.255.0.0', Subnet::cidrToIp(16));
        $this->assertEquals('255.255.255.0', Subnet::cidrToIp(24));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::containsAddress()
     */
    public function testContainsAddress()
    {
        $subnet = new Subnet('192.168.1.0/24');

        $this->assertTrue($subnet->contains('192.168.1.100'));
        $this->assertFalse($subnet->contains('127.0.0.1'));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::contains()
     */
    public function testContains()
    {
        $this->assertTrue(Subnet::containsAddress('192.168.1.0/24', '192.168.1.100'));
        $this->assertFalse(Subnet::containsAddress('192.168.1.0/24', '127.0.0.1'));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::getIterator()
     */
    public function testGetIterator()
    {
        $subnet = new Subnet('192.168.1.0/24');

        $this->assertInstanceOf(\Iterator::class, $subnet->getIterator());

        // Test that we can actually iterate
        foreach ($subnet as $ip) {
            return;
        }

        $this->fail('Failed to iterate');

    }
}
