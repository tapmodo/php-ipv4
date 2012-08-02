<?php /* vim: set ts=2 sw=2 tw=0 et :*/

class Ipv4_SubnetIterator implements Iterator
{
  private $position = 0;
  private $low_dec;
  private $hi_dec;

  public function __construct(Ipv4_Subnet $subnet) {
    $first = $subnet->getFirstHostAddr();
    $this->low_dec = Ipv4_Address::fromDottedQuad($first)->toDecimal();
    $last = $subnet->getLastHostAddr();
    $this->hi_dec = Ipv4_Address::fromDottedQuad($last)->toDecimal();
  }

  function rewind() {
    $this->position = 0;
  }

  function current() {
    return long2ip($this->low_dec + $this->position);
  }

  function key() {
    return $this->position;
  }

  function next() {
    ++$this->position;
  }

  function valid() {
    return (($this->low_dec + $this->position) <= $this->hi_dec);
  }
}

