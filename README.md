# IPv4 classes for PHP5

### Purpose

Identify, convert, and enumerate IPv4 IP addresses and subnets

### Installation

### Examples

    use ColinODell\Ipv4\Address;
    use ColinODell\Ipv4\Subnet;

    $ip = Address::fromString('10.2.1.1');
    $sn = Subnet::fromString('10.2.0.0/16');

    // Test if IP is in subnet
    $sn->contains($ip)          // true
    $sn->contains('10.3.1.23')  // false
    Subnet::ContainsAddress($sn,$ip)
    Subnet::ContainsAddress('192.168.1.0/27','192.168.1.246')

    // Test if two IPs are on the same network
    $netmask = '255.255.255.0';
    Subnet::ContainsAddress(new Subnet($ip1,$netmask),$ip2)

    // Can be written in numerous ways...
    Subnet::ContainsAddress("{$ip1}/24",$ip2)
    Subnet::fromString("{$ip1}/24")->contains($ip2)

    // Subnet information
    $sn->getNetwork()
    $sn->getNetmask()
    $sn->getNetmaskCidr()
    $sn->getFirstHostAddr()
    $sn->getLastHostAddr()
    $sn->getBroadcastAddr()

    // Enumerate subnet addresses
    $ips = $sn->getIterator();
    foreach($ips as $addr) ...

    // Count number of usable IPs on subnet (implements Countable)
    $sn->getTotalHosts()
    $sn->count()
    count($sn)
