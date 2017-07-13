<?php

namespace ColinODell\Ipv4\Test;

use ColinODell\Ipv4\Address;
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
     * @covers \ColinODell\Ipv4\Subnet::setFromString()
     * @covers \ColinODell\Ipv4\Subnet::setNetwork()
     * @covers \ColinODell\Ipv4\Subnet::setNetmask()
     */
    public function testConstructorGettersAndEquals()
    {
        $subnets = array();
        $subnets[] = new Subnet('192.168.1.0/24');
        $subnets[] = new Subnet('192.168.1.0', '255.255.255.0');
        $subnets[] = new Subnet(Address::fromString('192.168.1.0'), Address::fromString('255.255.255.0'));
        $subnets[] = new Subnet('192.168.1.0 255.255.255.0');
        $subnets[] = Subnet::fromString('192.168.1.0/24');
        $subnets[] = Subnet::fromString('192.168.1.0 255.255.255.0');

        $previousSubnet = null;
        foreach ($subnets as $subnet) {
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

            if ($previousSubnet !== null) {
                $this->assertTrue($subnet->equals($previousSubnet));
            }

            $previousSubnet = $subnet;
        }
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::__construct()
     */
    public function testInvalidConstructor()
    {
        $this->setExpectedException('InvalidArgumentException');
        $subnet = new Subnet('abc.123/45');
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::__construct()
     * @covers \ColinODell\Ipv4\Subnet::setNetmask()
     */
    public function testInvalidConstructorNetmask()
    {
        $this->setExpectedException('InvalidArgumentException');
        $subnet = new Subnet('192.168.1.0', '12.34.56.78');
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::cidrToIp()
     */
    public function testCidrToIp()
    {
        $this->assertEquals('0.0.0.0', Subnet::cidrToIp(0));
        $this->assertEquals('255.192.0.0', Subnet::cidrToIp(10));
        $this->assertEquals('255.255.0.0', Subnet::cidrToIp(16));
        $this->assertEquals('255.255.255.0', Subnet::cidrToIp(24));
        $this->assertEquals('255.255.255.255', Subnet::cidrToIp(32));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::cidrToIp()
     */
    public function testCidrToIpWithTooLowValue()
    {
        $this->setExpectedException('InvalidArgumentException');
        Subnet::cidrToIp(-1);
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::cidrToIp()
     */
    public function testCidrToIpWithTooHighValue()
    {
        $this->setExpectedException('InvalidArgumentException');
        Subnet::cidrToIp(33);
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::contains()
     */
    public function testContains()
    {
        $subnet = new Subnet('192.168.1.0/24');

        $this->assertTrue($subnet->contains('192.168.1.100'));
        $this->assertFalse($subnet->contains('127.0.0.1'));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::containsAddress()
     */
    public function testContainsAddress()
    {
        $this->assertTrue(Subnet::containsAddress('192.168.1.0/24', '192.168.1.100'));
        $this->assertTrue(Subnet::containsAddress('192.168.1.0/24', Address::fromString('192.168.1.100')));
        $this->assertTrue(Subnet::containsAddress(new Subnet('192.168.1.0/24'), '192.168.1.100'));
        $this->assertTrue(Subnet::containsAddress(new Subnet('192.168.1.0/24'), Address::fromString('192.168.1.100')));

        $this->assertFalse(Subnet::containsAddress('192.168.1.0/24', '127.0.0.1'));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::containsAddress()
     */
    public function testContainsAddressWithInvalidSubnetArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        Subnet::containsAddress(Address::fromString('192.168.1.1'), '192.168.1.1');
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::containsAddress()
     */
    public function testContainsAddressWithInvalidAddressArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        Subnet::containsAddress('192.168.1.0/24', new Subnet('192.168.1.0/24'));
    }

    /**
     * @covers \ColinODell\Ipv4\Subnet::getIterator()
     */
    public function testGetIterator()
    {
        $subnet = new Subnet('192.168.1.0/24');

        $this->assertInstanceOf('Iterator', $subnet->getIterator());

        // Test that we can actually iterate
        foreach ($subnet as $ip) {
            return;
        }

        $this->fail('Failed to iterate');
    }
}
