<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Ezdefi\Poc\TransactionPocToken;



$data = [
    'transaction_data' => array(
        'addressContract' => '0x14cCf9F6653Eac614a377eE827f0520601D3e68C',
        'privateKey' => 'e6cba9375d93cd4356dfcacfba7e4dcdbd3b8c0868f6b6de6ccc7a5a799955b1',
        'addressTo' => '0x524861A251f02ef0c31ab67326D59E6465990f9D',
        'chainId' => 66666,
        'gas' => 500000,
        'gasPrice' => '1000000',
        'value' => 0,
    ),
    'rpc_config' => array(
        'url' => 'https://rpc.nexty.io',
        'abi_json_file_path' => 'http://localhost/ezdefi-send-token/poc_token_abi.json',
        'name_abi' => 'transfer'
//        'abi_json_file_path' => 'http://localhost/ezdefi-send-token/poc_pool_abi.json',
//        'name_abi' => 'addTransaction'
    ),
    'param' => array(
        'addressTo'=> '0x524861A251f02ef0c31ab67326D59E6465990f9D',
        'amount'=> '0.0001'
//        '_uid'          => 'datpt007-2305-nang-day-san-truong-va-anh-noi-that-diu-dang-nho-noi-hang-cay-nao-mang-ten-em-trong-giac-mo',
//        '_username'     => 'dat.com.vn-hoi-em-yeu-anh-chi-muon-noi-mot-dieu-rang-anh-yeu-em-nhieu-lam-em-co-biet-khong-tinh-yeu-anh-danh-cho-em-la-mai-mai-ha-ha',
//        '_ref_by'       => 'pocadmin',
//        '_amount'       => '0',
//        '_merchant'     => 'dat.com.vn-hoi-em-yeu-anh-chi-muon-noi-mot-dieu-rang-anh-yeu-em-nhieu-lam-em-co-biet-khong-tinh-yeu-anh-danh-cho-em-la-mai-mai-ha-ha',
//        '_subid'        => '',
//        '_release'      => '5F52F82C',
//        '_ref_rates'    =>  ['2710', '0', '0', '0', '0', '0', '0', '0', '0', '0',],
    )
];

var_dump((new \Ezdefi\Poc\Client())->sendTransaction($data));