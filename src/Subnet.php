<?php

namespace ColinODell\Ipv4;

/**
 * Class for identifying and enumerating an IPv4 Subnet
 */
class Subnet implements \Countable, \IteratorAggregate
{
    /**
     * Define some error messages as class constants
     */
    const ERROR_NETWORK_FORMAT = 'IP format incorrect';
    const ERROR_CIDR_FORMAT = 'Invalid CIDR format';
    const ERROR_SUBNET_FORMAT = 'Invalid Subnet format';

    /**
     * Internal storage of network in long format
     *
     * @var float
     */
    private $nw = 0;

    /**
     * Internal storage of subnet in long format
     *
     * @var float
     */
    private $sn = 0;

    /**
     * Public constructor
     *
     * @param mixed $n Network
     * @param mixed $s Subnet
     */
    public function __construct($n = null, $s = null)
    {
        if ($n instanceof Address) {
            $n = $n->toString();
        }

        if ($s instanceof Address) {
            $s = $s->toString();
        }

        if (is_string($n) and !$s) {
            $this->setFromString($n);
        } elseif ($n and $s) {
            $this->setNetwork($n)->setNetmask($s);
        }
    }

    /**
     * Static method converts CIDR to dotted-quad IP notation
     *
     * @param int $cidr
     *
     * @return string
     */
    public static function cidrToIp($cidr)
    {
        if (!($cidr >= 0 and $cidr <= 32)) {
            throw new \InvalidArgumentException(self::ERROR_CIDR_FORMAT);
        }

        return long2ip(bindec(str_pad(str_pad('', $cidr, '1'), 32, '0')));
    }

    /**
     * Static method to determine if an IP is on a subnet
     *
     * @param mixed $sn
     * @param mixed $ip
     *
     * @return bool
     */
    public static function containsAddress($sn, $ip)
    {
        if (is_string($sn)) {
            $sn = new Subnet($sn);
        }

        if (is_string($ip)) {
            $ip = Address::fromString($ip);
        }

        if (!$sn instanceof Subnet) {
            throw new \InvalidArgumentException(self::ERROR_SUBNET_FORMAT);
        }

        if (!$ip instanceof Address) {
            throw new \InvalidArgumentException(Address::ERROR_ADDR_FORMAT);
        }

        $sn_dec = ip2long($sn->getNetmask());

        return (($ip->toLong() & $sn_dec) == (ip2long($sn->getNetwork()) & $sn_dec));
    }

    /**
     * Parse subnet string
     *
     * @param string $data
     *
     * @return self
     */
    private function setFromString($data)
    {
        // Validate that the input matches an expected pattern
        if (!preg_match('!^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(/[0-9]{1,2}))$!', $data)) {
            throw new \InvalidArgumentException(self::ERROR_NETWORK_FORMAT);
        }

        // Parse one of two formats possible, first is /CIDR format
        if (strpos($data, '/')) {
            list($network, $cidr) = explode('/', $data, 2);

            $this->setNetwork($network);
            $this->sn = ip2long(self::cidrToIp($cidr));
        } else {
            // Second format is network space subnet
            list($network, $subnet) = explode(' ', $data, 2);

            $this->setNetwork($network);
            $this->setNetmask($subnet);
        }

        return $this;
    }

    /**
     * Method to check if an IP is on this network
     *
     * @param mixed $ip
     *
     * @return bool
     */
    public function contains($ip)
    {
        return self::containsAddress($this, $ip);
    }

    /**
     * Set the network on the object, from dotted-quad notation
     *
     * @param string $data
     *
     * @return self
     */
    private function setNetwork($data)
    {
        $this->nw = Address::fromString($data)->toLong();

        return $this;
    }

    /**
     * Set the netmask on the object, from dotted-quad notation
     *
     * @param string $data
     *
     * @return self
     */
    private function setNetmask($data)
    {
        $data = Address::fromString($data);

        if (!preg_match('/^1*0*$/', $data->toBinary())) {
            throw new \InvalidArgumentException(self::ERROR_SUBNET_FORMAT);
        }

        $this->sn = $data->toLong();

        return $this;
    }

    /**
     * Return the netmask as dotted-quad string
     *
     * @return string
     */
    public function getNetmask()
    {
        return long2ip($this->sn);
    }

    /**
     * Return the CIDR value representing the netmask
     *
     * @return int
     */
    public function getNetmaskCidr()
    {
        return strlen(rtrim(decbin($this->sn), '0'));
    }

    /**
     * Return the network address in dotted-quad notation
     *
     * @return string
     */
    public function getNetwork()
    {
        $nw_bin = Address::fromLong($this->nw)->toBinary();
        $nw_bin = (str_pad(substr($nw_bin, 0, $this->getNetmaskCidr()), 32, 0));

        return Address::fromBinary($nw_bin)->toString();
    }

    /**
     * Return the first address of this network
     *
     * @return string
     */
    public function getFirstHostAddr()
    {
        $bin_net = Address::fromString($this->getNetwork())->toBinary();
        $bin_first = (str_pad(substr($bin_net, 0, 31), 32, 1));

        return Address::fromBinary($bin_first)->toString();
    }

    /**
     * Return last host of this network
     *
     * @return string
     */
    public function getLastHostAddr()
    {
        $bin_bcast = Address::fromString($this->getBroadcastAddr())->toBinary();
        $bin_last = (str_pad(substr($bin_bcast, 0, 31), 32, 0));

        return Address::fromBinary($bin_last)->toString();
    }

    /**
     * Return the broadcast address for this network
     *
     * @return string
     */
    public function getBroadcastAddr()
    {
        $bin_host = Address::fromLong($this->nw)->toBinary();
        $bin_bcast = str_pad(
            substr($bin_host, 0, $this->getNetmaskCidr()),
            32,
            1
        );

        return Address::fromBinary($bin_bcast)->toString();
    }

    /**
     * Return a count of the total number of hosts on this network
     *
     * @return int
     */
    public function getTotalHosts()
    {
        return (bindec(str_pad('', (32 - $this->getNetmaskCidr()), 1)) - 1);
    }

    /**
     * Return an iterator for addresses in this subnet
     *
     * @return SubnetIterator
     */
    public function getIterator()
    {
        return new SubnetIterator($this);
    }

    /**
     * Magic method prints subnet in IP/cidr format
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s/%s', $this->getNetwork(), $this->getNetmaskCidr());
    }

    /**
     * Implements Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->getTotalHosts();
    }

    /**
     * @param self $subnet
     *
     * @return bool
     */
    public function equals(self $subnet)
    {
        return $this->getNetwork() === $subnet->getNetwork() && $this->getNetmaskCidr() === $subnet->getNetmaskCidr();
    }
}
