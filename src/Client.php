<?php
namespace Ezdefi\Poc;

use Ezdefi\Poc\Contracts\RPCInterface;
use Ezdefi\Poc\Contracts\TransactionInterface;
use Ezdefi\Poc\Traits\RPCTraits;
class Client
{
    use RPCTraits;

	public $rpc = null;

	public $transaction = null;

	public function sendTransaction(array $data)
	{
        $transaction = $this->getTransaction();
        $RPC = $this->getRPC();

        if(empty($data['rpc_config']['abi_json_file_path_token']) || !is_string($data['rpc_config']['abi_json_file_path_token'])){
            throw new \Exception('Invalid file path abi json token');
        }

        $abiDataToken   = $this->readFileJson($data['rpc_config']['abi_json_file_path_token']);

        if(!is_array($abiDataToken)) {
            throw new \Exception('Invalid read file abi Json');
        }

        $data['rpc_config']['abi_data_token'] = $abiDataToken;

        unset($data['rpc_config']['abi_json_file_path_token']);

        if($data['rpc_config']['name_abi'] === 'addTransaction') {

            if(empty($data['rpc_config']['abi_json_file_path_pool']) || !is_string($data['rpc_config']['abi_json_file_path_pool'])){
                throw new \Exception('Invalid file path abi json pool');
            }

            $abiDataPool = $this->readFileJson($data['rpc_config']['abi_json_file_path_pool']);

            if(!is_array($abiDataPool)) {
                throw new \Exception('Invalid read file abi Json');
            }

            $data['rpc_config']['abi_data_pool'] = $abiDataPool;

            unset($data['rpc_config']['abi_json_file_path_pool']);

            if(empty($data['transaction_data']['addressContractPool']) || !is_string($data['transaction_data']['addressContractPool'])){
                throw new \Exception('Invalid address contract pool');
            }

            $data['param']['addressContractPool'] = $data['transaction_data']['addressContractPool'];
        }

        $RPC->setConfig($data['rpc_config']);

        try {
            $data['transaction_data']['transactionData'] = $RPC->getDataInTransaction(
                $data['rpc_config']['name_abi'],
                $data['param']
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if(empty($data['transaction_data']['privateKey']) || !is_string($data['transaction_data']['privateKey'])) {
            throw new \Exception('Invalid privateKey');
        }

        $data['transaction_data']['name_abi'] = $data['rpc_config']['name_abi'];

        $data['transaction_data']['nonce'] = $RPC->getTransactionCount($transaction->getAddress($data['transaction_data']['privateKey']));

		$signedData = $transaction->sign($data['transaction_data']);

		if( $signedData < 2 ){
		    return $RPC->sendRawTransaction($signedData);
        }

		$hash = [];
		foreach ( $signedData as $key => $item ) {
            $hash[] = $RPC->sendRawTransaction($item);
        }
		return $hash[count($signedData) - 1];
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
        if(!$dataJson) {
            throw new \Exception('Get file error');
        }
        return json_decode($dataJson);
    }

    public function amountToWei($amount)
    {
        $amount = strval($amount);
        $amount = $this->toWei($amount);
        $amountHex = $this->bcdechex($amount);
        return $amountHex;
    }
}