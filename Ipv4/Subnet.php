<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_Subnet
{
  private $nw, $sn;

  const ERROR_NETWORK_FORMAT = 'IP format incorrect';
  const ERROR_CIDR_FORMAT = 'Invalid CIDR format';
  const ERROR_SUBNET_FORMAT = 'Invalid Subnet format';

  function __construct($n=null,$s=null)
  {
    if (is_string($n) and !$s) $this->fromString($n);
      elseif ($n and $s) $this->setNetwork($n)->setNetmask($s);
  }
  function fromString($data)
  {
    if (!preg_match('!^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(/[0-9]{1,2}))$!',$data))
      throw new Exception(self::ERROR_NETWORK_FORMAT);
    if (strpos($data,'/'))
    {
      list($network,$cidr) = explode('/',$data,2);

      if (!($cidr >= 0 && $cidr <= 32))
        throw new Exception(self::ERROR_CIDR_FORMAT);

      $this->setNetwork($network);
      $this->sn = bindec(str_pad(str_pad("", $cidr, "1"), 32, "0"));
    } else {
      list($network,$subnet) = explode(' ',$data,2);
      $this->setNetwork($network);
      $this->setNetmask($subnet);
    }
  }
  function setNetwork($data)
  {
    $this->nw = Ipv4_Address::fromDottedQuad($data)->toDecimal();
    return $this;
  }
  function setNetmask($data)
  {
    $data = Ipv4_Address::fromDottedQuad($data);

    if (!preg_match('/^1*0*$/',$data->toBinary()))
      throw new Exception(self::ERROR_SUBNET_FORMAT);

    $this->sn = $data->toDecimal();
    return $this;
  }
  
  function getNetmask()
  {
    return Ipv4_Address::fromDecimal($this->sn)->toDottedQuad();
  }
  
  function getNetmaskCidr()
  {
    return strlen(rtrim(decbin($this->sn),'0'));
  }
  
  function getNetwork()
  {
    $nw_bin = Ipv4_Address::fromDecimal($this->nw)->toBinary();
    $nw_bin = (str_pad(substr($nw_bin,0,$this->getNetmaskCidr()),32,0));
    return Ipv4_Address::fromBinary($nw_bin)->toDottedQuad();
  }
  
  function getFirstHostAddr()
  {
    $bin_net = Ipv4_Address::fromDottedQuad($this->getNetwork())->toBinary();
    $bin_first = (str_pad(substr($bin_net,0,31),32,1));
    return Ipv4_Address::fromBinary($bin_first)->toDottedQuad();
  }
  
  function getLastHostAddr()
  {
    $bin_bcast = Ipv4_Address::fromDottedQuad($this->getBroadcastAddr())->toBinary();
    $bin_last = (str_pad(substr($bin_bcast,0,31),32,0));
    return Ipv4_Address::fromBinary($bin_last)->toDottedQuad();
  }
  
  function getBroadcastAddr()
  {
    $bin_host = Ipv4_Address::fromDecimal($this->nw)->toBinary();
    $bin_bcast = str_pad(substr($bin_host,0,$this->getNetmaskCidr()),32,1);
    return Ipv4_Address::fromBinary($bin_bcast)->toDottedQuad();
  }
  
  function getTotalHosts()
  {
    return (bindec(str_pad("",(32-$this->getNetmaskCidr()),1)) - 1);
  }
  
  function getIterator()
  {
    return new Ipv4_SubnetIterator($this);
  }
  
  function __toString()
  {
    return sprintf(
      '%s/%s',
      $this->getNetwork(),
      $this->getNetmaskCidr()
    );
  }
}

