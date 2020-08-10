<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Ezdefi\Poc\TransactionPocToken;


// khoi tao thong tin
$addressContract = '0x14cCf9F6653Eac614a377eE827f0520601D3e68C';
$privateKey = 'fdd3c6afb37dcdc31248bee65e0fccbf13146bb8e45fb6a59d2ac23471318715'; // NTY
$addressTo = '0x488BB2644b909Dc74ADCCFB2C67E89FEBDcd005F';
$amount = '0.007';

$chainId = 66666;
$url = 'https://rpc.nexty.io';
$file = 'file.json';

$transactionPocToken = new TransactionPocToken();
//$dataAbiFromAbiJson = $transactionPocToken->readFileJson('http://localhost/ezdefi-send-token/poc_token_abi.json') ;
//$dataInTransaction = $transactionPocToken->dataInTransaction($dataAbiFromAbiJson, 'transfer', $addressTo, $amount);
//$signTransaction = $transactionPocToken->signTransaction($privateKey, $addressContract, $dataInTransaction, $chainId);
//$transactionHash = $transactionPocToken->transactionHash($signTransaction);

$transactionHash = $transactionPocToken->sendTransactionToken($privateKey, $amount, $addressTo, $addressContract, $chainId);
echo $transactionHash;



