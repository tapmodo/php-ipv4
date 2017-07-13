<?php

namespace ColinODell\Ipv4;

/**
 * An object that implements a subnet iterator
 */
class SubnetIterator implements \Iterator
{
    private $position = 0;
    private $low_dec;
    private $hi_dec;

    /**
     * SubnetIterator constructor.
     *
     * @param Subnet $subnet
     */
    public function __construct(Subnet $subnet)
    {
        $this->low_dec = ip2long($subnet->getFirstHostAddr());
        $this->hi_dec = ip2long($subnet->getLastHostAddr());
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return long2ip($this->low_dec + $this->position);
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return (($this->low_dec + $this->position) <= $this->hi_dec);
    }
}

