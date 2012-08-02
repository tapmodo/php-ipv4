<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_Subnet
{
  /**
   * Define some error messages as class constants
   */
  const ERROR_NETWORK_FORMAT = 'IP format incorrect';
  const ERROR_CIDR_FORMAT = 'Invalid CIDR format';
  const ERROR_SUBNET_FORMAT = 'Invalid Subnet format';

  /**
   * nw
   * Internal storage of network in long format
   *
   * @var float
   * @access private
   */
  private $nw = 0;

  /**
   * sn
   * Internal storage of subnet in long format
   *
   * @var float
   * @access private
   */
  private $sn = 0;

  /**
   * __construct
   * Public constructor
   *
   * @param mixed $n Network
   * @param mixed $s Subnet
   * @access public
   * @return void
   */
  public function __construct($n=null,$s=null) {
    if (is_string($n) and !$s) $this->fromString($n);
      elseif ($n and $s) $this->setNetwork($n)->setNetmask($s);
  }

  /**
   * CIDRtoIP
   * Static method converts CIDR to dotted-quad IP notation
   *
   * @param mixed $cidr
   * @static
   * @access public
   * @return string
   */
  static function CIDRtoIP($cidr) {
    if (!($cidr >= 0 && $cidr <= 32))
      throw new Exception(self::ERROR_CIDR_FORMAT);

    return long2ip(bindec(str_pad(str_pad('', $cidr, '1'), 32, '0')));
  }

  /**
   * fromString
   * Parse subnet string
   *
   * @param string $data
   * @access public
   * @return self
   */
  public function fromString($data) {
    // Validate that the input matches an expected pattern
    if (!preg_match('!^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(/[0-9]{1,2}))$!',$data))
      throw new Exception(self::ERROR_NETWORK_FORMAT);

    // Parse one of two formats possible, first is /CIDR format
    if (strpos($data,'/')) {
      list($network,$cidr) = explode('/',$data,2);

      $this->setNetwork($network);
      $this->sn = ip2long(self::CIDRtoIP($cidr));
    }
    // Second format is network space subnet
    else {
      list($network,$subnet) = explode(' ',$data,2);
      $this->setNetwork($network);
      $this->setNetmask($subnet);
    }

    return $this;
  }

  /**
   * setNetwork
   * Sets the network on the object, from dotted-quad notation
   *
   * @param string $data
   * @access public
   * @return self
   */
  public function setNetwork($data) {
    $this->nw = Ipv4_Address::fromDottedQuad($data)->toDecimal();
    return $this;
  }

  /**
   * setNetmask
   * Sets the netmask on the object, from dotted-quad notation
   *
   * @param string $data
   * @access public
   * @return self
   */
  public function setNetmask($data) {
    $data = Ipv4_Address::fromDottedQuad($data);

    if (!preg_match('/^1*0*$/',$data->toBinary()))
      throw new Exception(self::ERROR_SUBNET_FORMAT);

    $this->sn = $data->toDecimal();
    return $this;
  }

  /**
   * getNetmask
   * Returns the netmask as dotted-quad string
   *
   * @access public
   * @return string
   */
  public function getNetmask() {
    return Ipv4_Address::fromDecimal($this->sn)->toDottedQuad();
  }

  /**
   * getNetmaskCidr
   * Returns the CIDR value representing the netmask
   *
   * @access public
   * @return int
   */
  public function getNetmaskCidr() {
    return strlen(rtrim(decbin($this->sn),'0'));
  }

  /**
   * getNetwork
   * Returns the network address in dotted-quad notation
   *
   * @access public
   * @return string
   */
  public function getNetwork() {
    $nw_bin = Ipv4_Address::fromDecimal($this->nw)->toBinary();
    $nw_bin = (str_pad(substr($nw_bin,0,$this->getNetmaskCidr()),32,0));
    return Ipv4_Address::fromBinary($nw_bin)->toDottedQuad();
  }

  /**
   * getFirstHostAddr
   * Returns the first address of this network
   *
   * @access public
   * @return string
   */
  public function getFirstHostAddr() {
    $bin_net = Ipv4_Address::fromDottedQuad($this->getNetwork())->toBinary();
    $bin_first = (str_pad(substr($bin_net,0,31),32,1));
    return Ipv4_Address::fromBinary($bin_first)->toDottedQuad();
  }

  /**
   * getLastHostAddr
   * Returns last host of this network
   *
   * @access public
   * @return string
   */
  public function getLastHostAddr() {
    $bin_bcast = Ipv4_Address::fromDottedQuad($this->getBroadcastAddr())->toBinary();
    $bin_last = (str_pad(substr($bin_bcast,0,31),32,0));
    return Ipv4_Address::fromBinary($bin_last)->toDottedQuad();
  }

  /**
   * getBroadcastAddr
   * Returns the broadcast address for this network
   *
   * @access public
   * @return string
   */
  public function getBroadcastAddr() {
    $bin_host = Ipv4_Address::fromDecimal($this->nw)->toBinary();
    $bin_bcast = str_pad(substr($bin_host,0,$this->getNetmaskCidr()),32,1);
    return Ipv4_Address::fromBinary($bin_bcast)->toDottedQuad();
  }

  /**
   * getTotalHosts
   * Returns a count of the total number of hosts on this network
   *
   * @access public
   * @return int
   */
  public function getTotalHosts() {
    return (bindec(str_pad("",(32-$this->getNetmaskCidr()),1)) - 1);
  }

  /**
   * getIterator
   * Returns an iterator for addresses in this subnet
   *
   * @access public
   * @return Ipv4_SubnetIterator
   */
  public function getIterator() {
    return new Ipv4_SubnetIterator($this);
  }

  /**
   * __toString
   * Magic method prints subnet in IP/cidr format
   *
   * @access public
   * @return string
   */
  public function __toString() {
    return sprintf(
      '%s/%s',
      $this->getNetwork(),
      $this->getNetmaskCidr()
    );
  }
}
