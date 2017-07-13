<?php

namespace ColinODell\Ipv4;

class Address
{
    /**
     * @var int
     */
    private $ip_long;

    const ERROR_ADDR_FORMAT = 'IP address string format error';

    /**
     * Create Address object from a standard dotted-quad IP address
     *
     * @param string $data
     *
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString($data)
    {
        if ($long = ip2long($data)) {
            return new self($long);
        }

        throw new \InvalidArgumentException(self::ERROR_ADDR_FORMAT);
    }

    /**
     * Create Address object from a decimal (long) address
     *
     * @param int $data
     *
     * @return self
     */
    public static function fromLong($data)
    {
        return new self((real)$data);
    }

    /**
     * Create Address object from a binary address
     *
     * @param string $data
     *
     * @return self
     */
    public static function fromBinary($data)
    {
        return new self(bindec($data));
    }

    /**
     * Return value as dotted quad IP address
     *
     * @return string
     */
    public function toString()
    {
        return long2ip($this->ip_long);
    }

    /**
     * Return value as decimal (long) address
     *
     * @return float
     */
    public function toLong()
    {
        return $this->ip_long;
    }

    /**
     * Return binary representation of address
     *
     * @return string
     */
    public function toBinary()
    {
        return str_pad(decbin($this->ip_long), 32, 0, STR_PAD_LEFT);
    }

    /**
     * Return dotted quad IP address
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Private constructor
     *
     * @param float $long
     */
    private function __construct($long)
    {
        $this->ip_long = $long;
    }
}
