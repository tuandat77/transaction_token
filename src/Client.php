<?php

class Client
{
	public $rpc = null;

	public $transaction = null;

	public function sendTransaction(array $data)
	{
		$transactionData = $this->rpc->getTransactionData($data);

		$signedData = $this->transaction->sign($transactionData);

		return $this->rpc->sendRawTransaction($signedData);
	}

	public function getRPC()
	{
		// If null
			// return RPC instance
	}

	public function setRPC(RPCInterface $rpc) : RPCInterface
	{
		$this->rpc = $rpc;
	}

	public function getTransaction()
	{
		// If null
			// return Transaction instance
	}

	public function setTransaction(TransactionInterface $transaction) : TransactionInterface
	{
		$this->transaction = $transaction;
	}
}