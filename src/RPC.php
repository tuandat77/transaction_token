<?php
namespace Ezdefi\Poc;

use Ezdefi\Poc\Contracts\RPCInterface;
use \Ezdefi\Poc\Traits\RPCTraits;
use Ethereum\Ethereum;
use Ethereum\DataType\EthD;
use Ethereum\DataType\EthD20;
use Ethereum\DataType\EthBlockParam;
use Ethereum\Abi;

class RPC implements RPCInterface
{
	use RPCTraits;
    protected $abiData;
    protected $addressTo;
    protected $amount;

    public function setData($data)
    {
        $this->amount = $data['amount'];
        $this->addressTo = $data['addressTo'];
    }

	public function sendRawTransaction(string $url, string $signTransaction)
	{
        return ( new Ethereum( $url ) )->eth_sendRawTransaction(
            new EthD("0x".$signTransaction)
        )->encodedHexVal();
	}

    public function getTransactionCount(string $url, string $data)
    {
        return ( new Ethereum( $url))->eth_getTransactionCount(
            new EthD20($data),
            new EthBlockParam()
        )->val();
	}

    public function getDataInTransaction(string $methodName)
    {
        return $this->refactorData(
            $this->getAbiClass()->encodeFunction(
                $methodName,
                array(
                    $this->getEthDataType('D', $this->addressTo),
                    $this->getEthDataType('D', $this->amountToWei())
                )
            )->encodedHexVal()
        );
	}

    protected function getEthDataType($type, $value)
    {
        $result = null;

        switch ($type) {
            case 'D20':
                $result = new EthD20($value);
                break;
            case 'D':
                $result = new EthD($value);
                break;
        }

        return $result;
    }

    public function getAbiData()
    {
        if(is_null($this->abiData)) {
            $this->abiData = $this->readFileJson('http://localhost/ezdefi-send-token/poc_token_abi.json');
        }

        return $this->abiData;
    }

    public function setAbiData(array $data)
    {
        $this->abiData = $data;
    }

    public function getAbiClass() : Abi
    {
        return new Abi(
            $this->getAbiData()
        );
    }

    public function amountToWei()
    {
        $amount = strval($this->amount);
        $amount = $this->toWei($amount);
        $amountHex = $this->bcdechex($amount);
        return $amountHex;
    }

}