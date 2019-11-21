<?php

$datas = [
    [
        'title' => 'Yandex',
        'model' => 'WalletYandexPS_Model_PaymentMethodsYandex',
        'type' => 'url',
        'state_name' => 't',
        'url' => 'walletyandexps/mobile_walletyandex/find',
        'code' => 'WalletYandexPS',
    ]
];

foreach ($datas as $data) {
    $method = new Wallet_Model_PaymentSystems();
    $method
        ->setData($data)
        ->insertOnce(['code']);
}
