<?php
namespace Ezdefi\Poc\Contracts;

interface TransactionInterface
{
    public function setData($data);

    public function getAddress();

	public function sign(string $data, string $nonce);

}