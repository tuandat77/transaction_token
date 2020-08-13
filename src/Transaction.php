<?php
namespace Ezdefi\Poc;

use Ezdefi\Poc\Contracts\TransactionInterface;
use Web3p\EthereumUtil\Util;
use Web3p\EthereumTx\Transaction as TransactionHandle;

class Transaction implements TransactionInterface
{
    protected $util;
    protected $privateKey;
    protected $addressContract;
    protected $chainId;
    protected $amount;
    protected $signTransactionData = array(
        'gas' => 200000,
        'gasPrice' => 0,
        'value' => 0,
    );

    public function __construct()
    {
        $this->util = new Util();
    }

    public function setData($data)
    {
        $this->privateKey = $data['privateKey'];
        $this->addressContract = $data['addressContract'];
        $this->chainId = $data['chainId'];
    }

	public function sign($data, $nonce)
	{
        $dataTransaction = array_merge([
            'nonce'     => $nonce,
            'from'      => $this->getAddress(),
            'to'        => $this->addressContract,
            'chainId'   => $this->chainId,
            'data'      => $data
        ], $this->getSignTransactionData());

        return (new TransactionHandle($dataTransaction))->sign($this->privateKey);
	}

    public function getAddress()
    {
        return $this->convertPrivateKeyToAddress();
    }

    public function convertPrivateKeyToAddress()
    {
        return $this->util->publicKeyToAddress(
            $this->util->privateKeyToPublicKey($this->privateKey)
        );
    }

    public function getSignTransactionData()
    {
        return $this->signTransactionData;
    }

    public function setSignTransactionData($data)
    {
        $this->signTransactionData = array_merge(
            $this->signTransactionData,
            $data
        );
    }

}