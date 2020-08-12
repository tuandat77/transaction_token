<?php
namespace Ezdefi\Poc;

use Web3p\EthereumTx\Transaction;

class TransactionHandle implements TransactionHandleInterface
{
    public function sign(array $dataInTransaction, string $privateKey)
    {
        return (new Transaction($dataInTransaction))->sign($privateKey);
    }
}