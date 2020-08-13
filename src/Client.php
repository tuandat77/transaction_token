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
        $transaction = $this->getTransaction();
        $RPC = $this->getRPC();

        $abiData = $this->readFileJson($data['rpc_config']['abi_json_file_path']);
        if(!is_array($abiData)) {
            throw new \Exception('co loi');
        }
        $data['rpc_config']['abi_data'] = $abiData;
        unset($data['rpc_config']['abi_json_file_path']);
        $RPC->setConfig($data['rpc_config']);

        // if not addressTo, if not amout

        $data['transaction_data']['transactionData'] = $RPC->getDataInTransaction(
            'transfer',
            $data['transaction_data']['addressTo'],
            $data['transaction_data']['amount']
        );

        // if not private key

        $data['transaction_data']['nonce'] = $RPC->getTransactionCount($transaction->getAddress($data['transaction_data']['privateKey']));

		$signedData = $transaction->sign($data['transaction_data']);

		return $RPC->sendRawTransaction($signedData);
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

    protected function readFileJson(string $path)
    {
        $dataJson = file_get_contents($path);

        return json_decode($dataJson);
    }
}