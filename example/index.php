<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Ethereum\Ethereum;
use Ezdefi\Poc\TransactionPocToken;


// khoi tao thong tin
$addressContract = '0x14cCf9F6653Eac614a377eE827f0520601D3e68C';
$privateKey = 'fdd3c6afb37dcdc31248bee65e0fccbf13146bb8e45fb6a59d2ac23471318715';
$addressTo = '0x488BB2644b909Dc74ADCCFB2C67E89FEBDcd005F';
$amount = '0.161';

$chainId = 66666;
$url = 'https://rpc.nexty.io';
$file = 'http://localhost/ezdefi-send-token/poc_token_abi.json';
$transfer = 'transfer';

$data = [
    'addressContract' => '0x14cCf9F6653Eac614a377eE827f0520601D3e68C',
    'privateKey' => 'e6cba9375d93cd4356dfcacfba7e4dcdbd3b8c0868f6b6de6ccc7a5a799955b1',
    'addressTo' => '0x524861A251f02ef0c31ab67326D59E6465990f9D',
    'amount' => '0.001',
    'chainId' => 66666
];

(new \Ezdefi\Poc\Client())->sendTransaction($data);