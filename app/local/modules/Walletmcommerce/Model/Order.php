<?php

/**
 * Walletmcommerce_Model_Order
 *
 */
class Walletmcommerce_Model_Order extends Core_Model_Default
{
    public function acceptBill($args)
    {
		$order = new Mcommerce_Model_Order();
		$order->find($args[0]);
		if ($order->getId()) {
			$order->setStatusId($args[1])->save();
		}
    }
	
    public function cancelBill($args)
    {
		$order = new Mcommerce_Model_Order();
		$order->find($args[0]);
		if ($order->getId()) {
			$order->setStatusId($args[1])->save();
		}
    }	
}
