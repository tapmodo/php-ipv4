<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_Address
{
  private $ip_long;
  const ERROR_ADDR_FORMAT = 'Dotted quad format error';

  /**
   * fromDottedQuad
   * Creates Ipv4_Address object from a standard dotted-quad IP address
   *
   * @param string $data
   * @static
   * @access public
   * @return Ipv4_Address
   */
  static function fromDottedQuad($data)
  {
    if ($long = ip2long($data)) return new self($long);
    throw new Exception(self::ERROR_ADDR_FORMAT);
  }

  /**
   * fromDecimal
   * Creates Ipv4_Address object from a decimal (long) address
   *
   * @param real $data
   * @static
   * @access public
   * @return Ipv4_Address
   */
  static function fromDecimal($data)
  {
    return new self((real)$data);
  }

  /**
   * fromBinary
   * Creates Ipv4_Address object from a binary address
   *
   * @param string $data
   * @static
   * @access public
   * @return Ipv4_Address
   */
  static function fromBinary($data)
  {
    return new self(bindec($data));
  }

  /**
   * toDottedQuad
   * Returns value as dotted quad IP address
   *
   * @access public
   * @return string
   */
  function toDottedQuad()
  {
    return long2ip($this->ip_long);
  }

  /**
   * toDecimal
   * Returns value as decimal (long) address
   *
   * @access public
   * @return real
   */
  function toDecimal()
  {
    return $this->ip_long;
  }

  /**
   * toBinary
   * Returns binary representation of address
   *
   * @access public
   * @return string
   */
  function toBinary()
  {
    return str_pad(decbin($this->ip_long),32,0,STR_PAD_LEFT);
  }

  /**
   * __toString
   * Magic method returns dotted quad IP address
   *
   * @access protected
   * @return string
   */
  function __toString()
  {
    return $this->toDottedQuad();
  }

  /**
   * __construct
   * Private constructor
   *
   * @param real $long
   * @access private
   * @return void
   */
  private function __construct($long)
  {
    $this->ip_long = $long;
  }

}
