<?php


namespace Ezdefi\Poc;

use Ethereum\Abi;
use Ethereum\DataType\EthD;
use Ezdefi\Poc\Exceptions\InvalidAddressContractException;
use Ezdefi\Poc\Exceptions\InvalidAddressToException;
use Ezdefi\Poc\Exceptions\InvalidAmountException;
use Ezdefi\Poc\Exceptions\InvalidPrivateKeyException;
use Web3p\EthereumTx\Transaction;
use Ethereum\DataType\EthD20;
use Ethereum\DataType\EthBlockParam;
use Ethereum\Ethereum;
use Web3p\EthereumUtil\Util;

class TransactionPocToken
{
    protected $transactionClient = null;

    public function sendTransactionToken(array $data)
    {
        $transactionClient = $this->getTransactionClient();

        $transactionClient->setData($data);

        return $transactionClient->transactionHash('transfer');
    }
    
    protected function getTransactionClient()
    {
        return new TransactionClient();
    }

    public function setTransactionClient()
    {
        if(is_null($this->transactionClient)) {
            $this->transactionClient = new TransactionClient();
        }

        return $this->transactionClient;
    }
}