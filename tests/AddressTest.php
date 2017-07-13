<?php

namespace ColinODell\Ipv4\Test;

use ColinODell\Ipv4\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \ColinODell\Ipv4\Address::fromString()
     * @covers \ColinODell\Ipv4\Address::toString()
     */
    public function testFromString()
    {
        $address = Address::fromString('127.0.0.1');
        $this->assertEquals('127.0.0.1', $address->toString());
    }

    /**
     * @covers \ColinODell\Ipv4\Address::fromLong()
     * @covers \ColinODell\Ipv4\Address::toLong()
     * @covers \ColinODell\Ipv4\Address::toString()
     */
    public function testFromLong()
    {
        $address = Address::fromLong(3564020356);
        $this->assertEquals(3564020356, $address->toLong());
        $this->assertEquals('212.110.162.132', $address->toString());
    }

    /**
     * @covers \ColinODell\Ipv4\Address::fromBinary()
     * @covers \ColinODell\Ipv4\Address::toBinary()
     * @covers \ColinODell\Ipv4\Address::toString()
     */
    public function testFromBinary()
    {
        $address = Address::fromBinary('11000000101010000000000100000001');
        $this->assertEquals('11000000101010000000000100000001', $address->toBinary());
        $this->assertEquals('192.168.1.1', $address->toString());
    }
}
