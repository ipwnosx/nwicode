App.factory('WalletmcommerceFactory', function($http, Url, $session, $window,$pwaRequest) {

    var factory = {};
    factory.value_id = null;

	
    factory.CreateOrder = function(data) {

        if(!this.value_id) {
            return $pwaRequest.reject("[WalletmcommerceFactory::createorder] missing value_id.");
        }

        return $pwaRequest.post("walletmcommerce/mobile_wallet/createorder", {
            urlParams: {
                value_id: this.value_id
            },
			cache: false,
			refresh: true
        });
    };	
	
    factory.ConfirmOrder = function(wallet_customer_id,wallet_bill_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[WalletmcommerceFactory::ConfirmOrder] missing value_id.");
        }

        return $pwaRequest.post("walletmcommerce/mobile_wallet/confirmorder", {
            urlParams: {
                value_id: this.value_id,
				wallet_bill_id:wallet_bill_id,
				wallet_customer_id:wallet_customer_id
            },
			cache: false,
			refresh: true
        });
    };	
	

    return factory;
}); 


