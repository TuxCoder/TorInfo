<?php

namespace TuxCoder\TorInfoBundle\Enum;


use MabeEnum\Enum;

class TorIpCheck extends Enum
{
    const REVERSE_DNS_ERROR = 0;
    const TOR_EXIT_ADDRESS_LIST = 1;
}