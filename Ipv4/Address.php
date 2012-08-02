<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_Address
{
  private $ip_long;
  const ERROR_ADDR_FORMAT = 'Dotted quad format error';

  static function fromDottedQuad($data)
  {
    if ($long = ip2long($data)) return new self($long);
    throw new Exception(self::ERROR_ADDR_FORMAT);
  }
  
  static function fromDecimal($data)
  {
    return new self((real)$data);
  }
  
  static function fromBinary($data)
  {
    return new self(bindec($data));
  }
  
  function toDottedQuad()
  {
    return long2ip($this->ip_long);
  }
  
  function toDecimal()
  {
    return $this->ip_long;
  }
  
  function toBinary()
  {
    return str_pad(decbin($this->ip_long),32,0,STR_PAD_LEFT);
  }
  

  private function __construct($long)
  {
    $this->ip_long = $long;
  }
  
}
