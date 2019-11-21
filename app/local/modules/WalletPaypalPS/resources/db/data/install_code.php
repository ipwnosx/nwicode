<?php

$datas = [
    [
        'title' => 'Paypal',
        'model' => 'WalletPaypalPS_Model_PaymentMethodsPaypal',
        'type' => 'url',
        'state_name' => 't',
        'url' => 'walletpaypalps/mobile_walletpaypal/find',
        'code' => 'WalletPaypalPS',
    ]
];

foreach ($datas as $data) {
    $method = new Wallet_Model_PaymentSystems();
    $method
        ->setData($data)
        ->insertOnce(['code']);
}
