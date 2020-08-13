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

    protected $config;

	public function sendRawTransaction(string $signTransaction)
	{
        return $this->getEthereumInstance()->eth_sendRawTransaction(
            $this->getEthDataType( 'D' , "0x".$signTransaction)
        )->encodedHexVal();
	}

    public function getTransactionCount(string $addressFrom)
    {
        return $this->getEthereumInstance()->eth_getTransactionCount(
            $this->getEthDataType('D20', $addressFrom),
            new EthBlockParam()
        )->val();
	}

	protected function getEthereumInstance()
    {
        return new Ethereum($this->config['url']);
    }

    public function getDataInTransaction(string $methodName, string $addressTo, string $amount)
    {
        return $this->refactorData(
            $this->getAbiClass()->encodeFunction(
                $methodName,
                array(
                    $this->getEthDataType('D', $addressTo),
                    $this->getEthDataType('D', $this->amountToWei($amount))
                )
            )->encodedHexVal()
        );
	}

    protected function getEthDataType($type, $value)
    {
        // View Ezdefi SDK
        $result = null;

        switch ($type) {
            case 'D20':
                try {
                    $result = new EthD20($value);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException($e->getMessage());
                }
                break;
            case 'D':
                try {
                    $result = new EthD($value);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException($e->getMessage());
                }

                break;
        }

        return $result;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getAbiClass() : Abi
    {
        return new Abi( $this->config['abi_data'] );
    }

    public function amountToWei($amount)
    {
        $amount = strval($amount);
        $amount = $this->toWei($amount);
        $amountHex = $this->bcdechex($amount);
        return $amountHex;
    }

}