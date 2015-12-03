<?php

namespace TuxCoder\TorInfoBundle;


use MabeEnum\EnumSet;
use Doctrine\Common\Cache\Cache;
use TuxCoder\TorInfoBundle\Enum\TorIpCheck;

class TorInfo
{

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache = null)
    {
        $this->cache = $cache;
    }


    /**
     * Checks an ip for part of the tor network
     *
     * @param $ip   string  Ip Address
     * @return EnumSet  contains no elements if the ip is not part of the tor network
     */
    public function torCheckIp($ip)
    {
        $errors = new EnumSet('TuxCoder\\TorInfoBundle\\Enum\\TorIpCheck');

        if (!$this->reverseDomainCheck($ip)) {
            $errors->attach(TorIpCheck::REVERSE_DNS_ERROR);
        }

        if (!$this->ExitAddressCheck($ip)) {
            $errors->attach(TorIpCheck::TOR_EXIT_ADDRESS_LIST);
        }

        return $errors;
    }

    /**
     * Be carefull with this function, can probably generate false positive
     *
     * @param $ip   string
     * @return bool returns false if the reverse domain contains the string 'tor', otherwise true
     */
    public function reverseDomainCheck($ip)
    {
        $hostname = gethostbyaddr($ip);
        return strpos($hostname, "tor") === false;
    }

    /**
     * @param $ip   string
     * @return bool return false if the ip is on a public exit node list
     */
    public function ExitAddressCheck($ip)
    {
        return strpos($this->getExitAddresses(), $ip) === false;

    }

    /**
     * @return string   returns a list of tor exit nodes, if cache is not null it caches the list
     */
    protected function getExitAddresses()
    {
        $key = "tuxcoder_tor_info_exit_addresses";

        if ($this->cache !== null && $this->cache->contains($key)) {
            return $this->cache->fetch($key);
        } else {
            $s = curl_init();

            curl_setopt($s, CURLOPT_URL, "https://check.torproject.org/exit-addresses");
            curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);

            $exitAddresses = curl_exec($s);
            if ($this->cache !== null) {
                $this->cache->save($key, $exitAddresses, 60 * 5);
            }

            return $exitAddresses;
        }
    }
}