<?php

namespace Ezdefi\Poc;

interface Web3ClientInterface
{

    public function getTransactionCount(string $url, string $address);

    public function sendRawTransaction(string $url, string $signTransaction);
}