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
    private $ethBlockParam;
    private $ethereum;
    private $util;
    private $convertAmount;

    public function __construct()
    {
        $this->ethBlockParam =  new EthBlockParam();
        $this->ethereum = new Ethereum();
        $this->util = new Util();
        $this->convertAmount = new Converter();
//        file_get_contents(dir(__FILE__) . 'poc_token_abi.json');
    }
    
    public function connectEth()
    {
        $eth = new Ethereum('https://rpc.nexty.io');
//        $eth = new Ethereum($url);
        return $eth;
    }

    public function readFileJson(String $fileName)
    {
//        $data_json = file_get_contents(dir(__FILE__).$fileName);
        $data_json = file_get_contents($fileName);
        return json_decode($data_json);
    }

    public function getPrivateKey()
    {
        if(empty($_REQUEST['privateKey'])) {
            throw new InvalidPrivateKeyException('PrivateKey to is required');
        }
        return $_REQUEST['privateKey'];
    }

    public function getMount()
    {
        if(empty($_REQUEST['amount'])) {
            throw new InvalidAmountException('Amount is required');
        }

        return $_REQUEST['amount'];
    }

    public function getAddressTo()
    {
        if(empty($_REQUEST['address_to'])) {
            throw new InvalidAddressToException('Address to is required');
        }

        return $_REQUEST['address_to'];
    }

    public function getAddressContract()
    {
        if(empty($_REQUEST['address_contract'])) {
            throw new InvalidAddressContractException('Address contract to is required');
        }

        return $_REQUEST['address_contract'];
    }

    public function getAddressTokenFromPrivateKey($privateKey)
    {
        return $this->convertToAddressToken($privateKey);
    }

    public function convertToAddressToken($privateKey)
    {
        $publicKey = $this->util->privateKeyToPublicKey($privateKey);
        $addressToken = $this->util->publicKeyToAddress($publicKey);

        return $addressToken;
    }

    public function getTransactionCount($privateKey) // nonce
    {
        $address = $this->getAddressTokenFromPrivateKey($privateKey);
        $address = new EthD20($address);
        $arg2 = new EthBlockParam();
        $countNonce = $this->connectEth()->eth_getTransactionCount($address, $arg2);
        $countNonce = $countNonce->value->value;
        $nonce = $countNonce;
        return (int)$nonce;
    }

    // convert from poc unit to Wei 1poc = 10^18 wei then convert to hex:
    public function ToWei($amount)
    {
        $amount = strval($amount);
        $amount = $this->getConverter()->toWei($amount);
        $amountHex = $this->bcdechex($amount);

        return $amountHex;
    }

    protected function getConverter()
    {
        return $this->convertAmount;
    }

    public function dataInTransaction(array $data, string $transfer, string $addressTo, $amount)
    {
        $abiClass = new Abi($data);
        $data = $abiClass->encodeFunction(
            $transfer,
          array(
              new EthD($addressTo),
              new EthD($this->ToWei($amount)),
          )
        );

        $dataInTransaction = $data->encodedHexVal();
        $dataInTransaction = $this->refactorDataForInTransaction($dataInTransaction);
        return $dataInTransaction;
    }

    public function signTransaction($privateKey, $addressContract, $data, $chainId )
    {
        $nonce = $this->getTransactionCount($privateKey);
        $addressFrom = $this->getAddressTokenFromPrivateKey($privateKey);

        $dataTransaction = [
            'nonce'         => $nonce,
            'from'          => $addressFrom,
            'to'            => $addressContract, // dia chi contract
            'gas'           => 100000,
            'gasPrice'      => 0,
            'value'         => 0,
            'chainId'       => $chainId,
            'data'          => $data
        ];

        $transaction = new Transaction($dataTransaction);

        $signTransaction = $transaction->sign($privateKey);
        return $signTransaction;

    }

    public function transactionHash($signTransaction)
    {
        $eth = $this->connectEth();
        $sendRawTransaction = $eth->eth_sendRawTransaction(new EthD("0x".$signTransaction));
        return $sendRawTransaction->encodedHexVal();
    }

    public function refactorDataForInTransaction($data)
    {
        $data1 = substr($data, 0, 10);
        $data2 = '000000000000000000000000';
        $data3 = substr($data, 10);
        $data = $data1.$data2.$data3;
        return $data;
    }

    public function sendTransactionToken($privateKey, $amount, $addressTo, $addressContract, $chainId)
    {
        $dataAbi = $this->readFileJson('http://localhost/ezdefi-send-token/poc_token_abi.json');
        $dataInTransaction = $this->dataInTransaction($dataAbi, 'transfer', $addressTo, $amount);
        $signTransaction = $this->signTransaction($privateKey, $addressContract, $dataInTransaction, $chainId);
        $transactionHash = $this->transactionHash($signTransaction);
        return $transactionHash;
    }

    public function bcdechex($dec) {
        $hex = '';
        do {
            $last = bcmod($dec, 16);
            $hex = dechex($last).$hex;
            $dec = bcdiv(bcsub($dec, $last), 16);
        } while($dec>0);
        return $hex;
    }
}