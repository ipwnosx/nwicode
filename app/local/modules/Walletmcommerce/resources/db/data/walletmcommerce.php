<?php
# Only minimal assets are required now!
$datas = [
	['code' => 'walletmcommerce', 'name' => 'Wallet', 'online_payment' => 1],
];

foreach ($datas as $data) {
    $method = new Mcommerce_Model_Payment_Method();
    $method
        ->setData($data)
        ->insertOnce(["code"]);
}
