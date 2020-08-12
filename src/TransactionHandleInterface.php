<?php
namespace Ezdefi\Poc;

interface TransactionHandleInterface
{
    public function sign(array $dataInTransaction, string $privateKey);
}