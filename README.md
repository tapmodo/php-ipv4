# IPv4 classes for PHP5

### Purpose

Identify, convert, and enumerate IPv4 IP addresses and subnets

### Examples

    $ip = Ipv4_Address::fromString('10.2.1.1');
    $sn = Ipv4_Subnet::fromString('10.2.0.0/16');

    // Test if IP is in subnet
    $sn->contains($ip)          // true
    $sn->contains('10.3.1.23')  // false
    Ipv4_Subnet::ContainsAddress($sn,$ip)
    Ipv4_Subnet::ContainsAddress('192.168.1.0/27','192.168.1.246')

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
