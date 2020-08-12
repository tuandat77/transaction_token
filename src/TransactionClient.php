<?php
namespace Ezdefi\Poc;

use Ethereum\Abi;
use Ethereum\DataType\EthBlockParam;
use Ethereum\DataType\EthD;
use Ethereum\DataType\EthD20;
use Ethereum\Ethereum;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumUtil\Util;
use Ezdefi\Poc\TransactionHandleInterface;

class TransactionClient implements TransactionClientInterface
{

    protected $privateKey;
    protected $amount;
    protected $addressTo;
    protected $addressContract;
    protected $chainId;
    protected $abiData;
    protected $web3Client = null;
    protected $signTransactionData = array(
        'gas' => 200000,
        'gasPrice' => 0,
        'value' => 0,
    );
    protected $transactionHandle;


    private $ethBlockParam;
    private $ethereum;
    protected $util;
    private $convertToWei;

    public function __construct()
    {

        $this->ethBlockParam = new EthBlockParam();
        $this->ethereum = new Ethereum();
        $this->util = new Util();
        $this->convertToWei = new Converter();
    }

    public function transactionHash(string $methodName = 'transfer')
    {
        // Validate $method
        // If not validated, throw exception

        $dataInTransaction = $this->getDataInTransaction($methodName);

        $signTransaction = $this->signTransaction($dataInTransaction);

        return $this->getWeb3Client()->sendRawTransaction('https://rpc.nexty.io', $signTransaction);
    }

    public function readFileJson($file)
    {
        $dataJson = file_get_contents($file);
        return json_decode($dataJson);
    }

    public function bcdechex($dec)
    {
        $hex = '';
        do {
            $last = bcmod($dec, 16);
            $hex  = dechex($last).$hex;
            $dec  = bcdiv(bcsub($dec, $last), 16);
        } while($dec > 0);
        return $hex;
    }

    // convert from poc unit to Wei 1poc = 10^18 wei then convert to hex
    public function amountToWei()
    {
        $amount = strval($this->amount);
        $amount = $this->getConverter()->toWei($amount);
        $amountHex = $this->bcdechex($amount);
        return $amountHex;
    }

    protected function getConverter()
    {
        return $this->convertToWei;
    }

    public function convertPrivateKeyToAddress()
    {
        return $this->util->publicKeyToAddress(
            $this->util->privateKeyToPublicKey($this->privateKey)
        );
    }

    public function getAddress()
    {
        return $this->convertPrivateKeyToAddress();
    }

    public function getDataInTransaction(string $transfer)
    {
        return $this->refactorDataForInTransaction(
            $this->encodeFunction($transfer)->encodedHexVal()
        );
    }

    protected function encodeFunction(string $methodName = 'transfer')
    {
        return $this->getAbiClass()->encodeFunction(
            $methodName,
            array(
                $this->getEthDataType('D', $this->addressTo),
                $this->getEthDataType('D', $this->amountToWei())
            )
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

    public function refactorDataForInTransaction($data)
    {
        $data1 = substr($data, 0, 10);
        $data2 = '000000000000000000000000';
        $data3 = substr($data, 10);
        $data = $data1 . $data2 . $data3;
        return $data;
    }

    public function setData($data)
    {
        $this->privateKey = $data['privateKey'];
        $this->amount = $data['amount'];
        $this->addressTo = $data['addressTo'];
        $this->addressContract = $data['addressContract'];
        $this->chainId = $data['chainId'];
    }

    public function setConfig()
    {
        // TODO: Implement setConfig() method.
    }

    public function signTransaction($data)
    {
        $nonce = $this->getTransactionCountFromBlock();

        $dataTransaction = array_merge([
            'nonce' => $nonce,
            'from' => $this->getAddress(),
            'to' => $this->addressContract,
            'chainId' => $this->chainId,
            'data' => $data
        ], $this->getSignTransactionData());

        return $this->getTransactionHandle()->sign($dataTransaction, $this->privateKey);

    }

    public function setTransactionHandle(TransactionHandleInterface $transactionHandle)
    {
        $this->transactionHandle = $transactionHandle;
    }

    public function getTransactionHandle() :TransactionHandleInterface
    {
        if(is_null($this->transactionHandle)) {
            $this->transactionHandle = new TransactionHandle();
        }

        return $this->transactionHandle;
    }

    public function getTransactionCountFromBlock()
    {
        return $this->getWeb3Client()->getTransactionCount(
            'https://rpc.nexty.io',
            $this->getAddress()
        );
    }



    public function setWeb3Client(Web3ClientInterface $web3Client)
    {
        $this->web3Client = $web3Client;
    }

    public function getWeb3Client() : Web3ClientInterface
    {
        if(is_null($this->web3Client)) {
            $this->web3Client = new EthereumWeb3Client();
        }

        return $this->web3Client;
    }

    public function setAbiData( array $data )
    {
        $this->abiData = $data;
    }

    public function getAbiData()
    {
        if(is_null($this->abiData)) {
            $this->abiData = $this->readFileJson('http://localhost/ezdefi-send-token/poc_token_abi.json');
        }

        return $this->abiData;
    }

    public function getAbiClass() : Abi
    {
        return new Abi(
            $this->getAbiData()
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