<?php

namespace Ezdefi\Poc;

use Ethereum\DataType\EthBlockParam;
use Ethereum\DataType\EthD;
use Ethereum\DataType\EthD20;
use Ethereum\Ethereum;

class EthereumWeb3Client implements Web3ClientInterface
{

    public function getTransactionCount(string $url, string $data)
    {
        return ( new Ethereum( $url))->eth_getTransactionCount(
            new EthD20($data),
            new EthBlockParam()
        )->val();
    }

    public function sendRawTransaction(string $url, string $signTransaction)
    {
        return ( new Ethereum( $url ) )->eth_sendRawTransaction(
            new EthD("0x".$signTransaction)
        )->encodedHexVal();
    }
}