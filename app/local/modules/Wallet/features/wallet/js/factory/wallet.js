angular.module("starter").factory('Wallet', function($pwaRequest) {
    var factory = {
        value_id: null
    };

    factory.find = function() {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::find] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/find", {
            urlParams: {
                value_id: this.value_id
            },
			cache: false,
			refresh: true
        });
    }; 
	factory.findTransactions = function(wallet_customer_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::findTransactions] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/findtransactions", {
            urlParams: {
                value_id: this.value_id,
				wallet_customer_id: wallet_customer_id
            },
			cache: false,
			refresh: true
        });
    };
	
	factory.findPayouts = function(wallet_customer_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::findpayouts] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/findpayouts", {
            urlParams: {
                value_id: this.value_id,
				wallet_customer_id: wallet_customer_id
            },
			cache: false,
			refresh: true
        });
    };	
	
	factory.cancelPayout = function(wallet_payout_request_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::cancelPayout] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/cancelpayout", {
            urlParams: {
                value_id: this.value_id,
				wallet_payout_request_id: wallet_payout_request_id
            },
			cache: false,
			refresh: true
        });
    };
	
	factory.makeRequest = function(wallet_payout_methods_id,summ, comment) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::makeRequest] missing value_id.");
        }

        return $pwaRequest.post("wallet/mobile_view/makerequest", {
            urlParams: {
                value_id: this.value_id,
            },
			data: {
                wallet_payout_methods_id: wallet_payout_methods_id,
				summ: summ,
				comment:comment
				
            },			
			cache: false,
			refresh: true
        });
    };

	factory.makeTransfer = function(to_account,summ, comment) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::makeTransfer] missing value_id.");
        }

        return $pwaRequest.post("wallet/mobile_view/maketransfer", {
            urlParams: {
                value_id: this.value_id,
            },
			data: {
                to_account: to_account,
				summ: summ,
				comment:comment
				
            },			
			cache: false,
			refresh: true
        });
    };	
	
	factory.findBills = function(wallet_customer_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::findBills] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/findbills", {
            urlParams: {
                value_id: this.value_id,
				wallet_customer_id: wallet_customer_id
            },
			cache: false,
			refresh: true
        });
    };	
	
	factory.findBill = function(wallet_bill_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::findBill] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/findbill", {
            urlParams: {
                value_id: this.value_id,
				wallet_bill_id: wallet_bill_id
            },
			cache: false,
			refresh: true
        });
    };
	
	factory.acceptBill = function(wallet_bill_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::acceptBill] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/acceptbill", {
            urlParams: {
                value_id: this.value_id,
				wallet_bill_id: wallet_bill_id
            },
			cache: false,
			refresh: true
        });
    };
	factory.cancelBill = function(wallet_bill_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[Wallet::cancelBill] missing value_id.");
        }

        return $pwaRequest.get("wallet/mobile_view/cancelbill", {
            urlParams: {
                value_id: this.value_id,
				wallet_bill_id: wallet_bill_id
            },
			cache: false,
			refresh: true
        });
    };		
    return factory;
});