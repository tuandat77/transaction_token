<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Ethereum\Ethereum;
use Ezdefi\Poc\TransactionPocToken;


// khoi tao thong tin
$addressContract = '0x14cCf9F6653Eac614a377eE827f0520601D3e68C';
$privateKey = 'e6cba9375d93cd4356dfcacfba7e4dcdbd3b8c0868f6b6de6ccc7a5a799955b1';
$addressTo = '0x524861A251f02ef0c31ab67326D59E6465990f9D';
$amount = '0.001';

$chainId = 66666;
$url = 'https://rpc.nexty.io';
$file = 'file.json';
$transfer = 'transfer';

$data = [
    'addressContract' => '0x14cCf9F6653Eac614a377eE827f0520601D3e68C',
'privateKey' => 'e6cba9375d93cd4356dfcacfba7e4dcdbd3b8c0868f6b6de6ccc7a5a799955b1',
'addressTo' => '0x524861A251f02ef0c31ab67326D59E6465990f9D',
'amount' => '0.001',
'chainId' => 66666
];

(new TransactionPocToken())->sendTransactionToken($data);