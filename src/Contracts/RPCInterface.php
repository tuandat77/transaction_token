<?php
namespace Ezdefi\Poc\Contracts;

interface RPCInterface
{
    public function setData($data);

	public function getTransactionCount(string $url, string $data);

    public function getDataInTransaction(string $methodName);

    public function sendRawTransaction(string $url, string $signTransaction);

}