<?php
namespace TuxCoder\TorInfoBundle\Tests;

use Doctrine\Common\Cache\ArrayCache;
use TuxCoder\TorInfoBundle\TorInfo;

class TorInfoTest extends \PHPUnit_Framework_TestCase
{

    public function testReverseDNS()
    {
        $torInfo = new TorInfo();

        $this->assertFalse($torInfo->reverseDomainCheck('176.58.100.98'), "Tor exit node not detected");
        $this->assertTrue($torInfo->reverseDomainCheck('64.15.113.187'), "False positiv detected google as tor exit node");
    }

    public function testTorExitNodeAddressList()
    {
        $torInfo = new TorInfo();

        $this->assertFalse($torInfo->ExitAddressCheck('176.58.100.98'), "Tor exit node not detected");
        $this->assertTrue($torInfo->ExitAddressCheck('64.15.113.187'), "False positiv detected google as tor exit node");
    }

    public function testTorIpCheck()
    {
        $torInfo = new TorInfo();

        $this->assertTrue($torInfo->torCheckIp('176.58.100.98')->count() == 2, "Tor exit node not detected");
        $this->assertTrue($torInfo->torCheckIp('64.15.113.187')->count() == 0, "False positiv detected google as tor exit node");
    }

    public function testCache()
    {
        $cache = new ArrayCache();
        $torInfo = new TorInfo($cache);

        $this->assertTrue($torInfo->torCheckIp('176.58.100.98')->count() == 2, "Tor exit node not detected");

        $this->assertTrue($cache->contains('tuxcoder_tor_info_exit_addresses'));
    }
}