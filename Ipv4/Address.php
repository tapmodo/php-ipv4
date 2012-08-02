<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_Address
{
  private $ip_long;
  const ERROR_ADDR_FORMAT = 'Dotted quad format error';

  static function fromDottedQuad($data)
  {
    if (!preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$data))
      throw new Exception(self::ERROR_ADDR_FORMAT);

    $octets = explode('.',$data);
    $rv = '';

    foreach($octets as $v)
      $rv.= str_pad(decbin($v),8,'0',STR_PAD_LEFT);

    return new self(bindec($rv));
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
    $bval = str_pad(decbin($this->ip_dec),32,'0',STR_PAD_LEFT);
    return implode('.',array_map('bindec',
        explode('.',rtrim(chunk_split($bval,8,'.'),'.'))));
  }
  
  function toDecimal()
  {
    return $this->ip_dec;
  }
  
  function toBinary()
  {
    return str_pad(decbin($this->ip_dec),32,0,STR_PAD_LEFT);
  }
  

  private function __construct($long)
  {
    $this->ip_dec = $long;
  }
  
}
