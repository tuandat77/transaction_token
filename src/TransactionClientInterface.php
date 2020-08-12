<?php
namespace Ezdefi\Poc;

interface TransactionClientInterface
{
    public function setData($data);

    public function setConfig();

//    public function signTransaction($privateKey, $addressContract, $data, $chainId);

    public function transactionHash(string $method = 'transafer');
}