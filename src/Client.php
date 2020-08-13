<?php
namespace Ezdefi\Poc;

use Ezdefi\Poc\Contracts\RPCInterface;
use Ezdefi\Poc\Contracts\TransactionInterface;

class Client
{
	public $rpc = null;

	public $transaction = null;

	public function sendTransaction(array $data)
	{
        $transactionClient = $this->getTransaction();
        $transactionClient->setData($data);

        $RPC = $this->getRPC();
        $RPC->setData($data);

		$addressFrom = $transactionClient->getAddress();

        $nonce = $RPC->getTransactionCount('https://rpc.nexty.io', $addressFrom);

        $transactionData = $RPC->getDataInTransaction('transfer');

		$signedData = $transactionClient->sign($transactionData, $nonce);

//		return $RPC->sendRawTransaction('https://rpc.nexty.io', $signedData);
		$dataHash = $RPC->sendRawTransaction('https://rpc.nexty.io', $signedData);
		var_dump($dataHash);
	}

	public function getRPC() :RPCInterface
	{
        if(is_null($this->rpc)){
            $this->rpc = new RPC();
        }

        return $this->rpc;
	}

	public function setRPC(RPCInterface $rpc)
	{
		$this->rpc = $rpc;
	}

	public function getTransaction() : TransactionInterface
	{
        if(is_null($this->transaction)) {
            $this->transaction = new Transaction();
        }

        return $this->transaction;
	}

	public function setTransaction(TransactionInterface $transaction)
	{
		$this->transaction = $transaction;
	}
}