<?php
namespace Ezdefi\Poc;

use Ezdefi\Poc\Contracts\TransactionInterface;
use Web3p\EthereumUtil\Util;
use Web3p\EthereumTx\Transaction as TransactionHandle;

class Transaction implements TransactionInterface
{
    protected $util;

    public function __construct()
    {
        $this->util = new Util();
    }

	public function sign($data)
	{
        $dataTransaction = [
            'nonce'     => $data['nonce'],
            'from'      => $this->getAddress($data['privateKey']),
            'to'        => $data['addressContract'],
            'chainId'   => $data['chainId'],
            'gas' => $data['gas'],
            'gasPrice' => $data['gasPrice'],
            'value' => $data['value'],
            'data'      => $data['transactionData']
        ];

        return (new TransactionHandle($dataTransaction))->sign($data['privateKey']);
	}

    public function getAddress(string $privateKey)
    {
        return $this->util->publicKeyToAddress(
            $this->util->privateKeyToPublicKey($privateKey)
        );
    }
}