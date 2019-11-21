/*global
 App, angular, BASE_PATH
 */
angular.module("starter").controller("WalletViewController", function ($log, $sce, $scope, Modal, $state,$stateParams, $timeout, $location, Wallet,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("WalletViewController fired");

	
	//Если пользователь не зарегестрирован, то отправим его на регистрацию
	$scope.is_logged_in = false;
	if (!Customer.isLoggedIn()) {
		/*console.log("Not logged, do redirect;");
		//Customer.loginModal();
		Customer.loginModal($scope, function() {
			if (Customer.isLoggedIn()) $scope.is_logged_in = true;
			console.log("login modal first func");
		}, function() {
			if (!Customer.isLoggedIn()) $scope.is_logged_in = false; 
			console.log("login modal second func");
		});	*/
	} else $scope.is_logged_in = true;	
	

	
	$scope.value_id = $stateParams.value_id;
	Wallet.value_id= $stateParams.value_id;
	$scope.customer_email = "";
	$scope.mr={};
	$scope.mr.request_amount = 0;
	$scope.mr.request_comment = "";
	$scope.mr.error_message = "";
	
	$scope.af={};
	$scope.af.amount = 0;
	$scope.af.addfund_method_current = "";
	
	$scope.tr={};
	$scope.tr.request_amount = 0;
	$scope.tr.request_comment = "";
	$scope.tr.to_account = "";	
	$scope.tr.error_message = "";	
	
	$scope.wallet_customer_id = 0;
	$scope.data = {};
	$scope.current_payout_method = {};
	$scope.current_payment_method = {};
	$scope.data.payout_method_current = 0;
	$scope.is_loading = true;
	$scope.old_style = true;
	$scope.request_modal = null;
	$scope.transfer_modal = null;
	$scope.addfunds_modal = null;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("Wallet loadContent.");
		
		if ($scope.is_logged_in) {
			Wallet.find().then(function (data) {
				console.log("Wallet.find:");
				console.log(data);
				$scope.data = data;
				$scope.wallet_customer_id = data.customer.wallet_customer_id;
			}).then(function () {
				$scope.is_loading = false;
				Loader.hide();
			});
		} else {
		
		
			$scope.is_loading = false;
			Loader.hide();			
		}
			
    };
    
	$scope.loadContent();

	$scope.transactionsWallet = function () {
		$state.go('wallet-transactions', {
			value_id: $scope.value_id,
			wallet_customer_id:$scope.wallet_customer_id
		});
	};
	
	$scope.payoutsWallet = function () {
		$state.go('wallet-payouts', {
			value_id: $scope.value_id,
			wallet_customer_id:$scope.wallet_customer_id
		});
	};
	$scope.billsWallet = function () {
		$state.go('wallet-bills', {
			value_id: $scope.value_id,
			wallet_customer_id:$scope.wallet_customer_id
		});
	};

	$scope.loginAction = function() {
		$scope.is_logged_in = false;
		if (!Customer.isLoggedIn()) {
			console.log("Not logged, do redirect;");
			//Customer.loginModal();
			Customer.loginModal($scope, function() {
				if (Customer.isLoggedIn()) $scope.is_logged_in = true;
				console.log("login modal first func");
			}, function() {
				if (!Customer.isLoggedIn()) $scope.is_logged_in = false; 
				console.log("login modal second func");
			});	
		} else $scope.is_logged_in = true;	
	
	};
	
	
	$scope.createRequest = function() {
		console.log("createRequest clicked:");
		Modal.fromTemplateUrl('features/wallet/assets/templates/l1/request.html', {
		  scope: $scope,
		  animation: 'none'
		}).then(function(modal) {
			$scope.request_modal = modal;
			$scope.request_modal.show();
		
		});			
	
	};
	
	
	$scope.PayoutMethodChange = function() {
		console.log("PayoutMethodChange clicked:");
		console.log($scope.data.payout_method_current);
		$scope.data.payment_methods.forEach(function(element) {
			if (element.wallet_payout_methods_id==$scope.data.payout_method_current) $scope.current_payout_method = element;
		});
		console.log($scope.current_payout_method);
	}
	
	$scope.openAddFunds = function() {
		console.log("openAddFunds clicked:");
		Modal.fromTemplateUrl('features/wallet/assets/templates/l1/payments_systems.html', {
		  scope: $scope,
		  animation: 'none'
		}).then(function(modal) {
			$scope.addfunds_modal = modal;
			$scope.addfunds_modal.show();
		
		});	
	}	
	
	$scope.addFundMethodChange = function() {
		console.log("addFundMethodChange clicked:");
		console.log($scope.af.addfund_method_current);
	}
	
	$scope.makeRequest = function() {
		$scope.is_loading = true;
		Loader.show();		
		Wallet.makeRequest($scope.current_payout_method.wallet_payout_methods_id,$scope.mr.request_amount, $scope.mr.request_comment).then(function (data) {
			console.log("Wallet.makeRequest:");
			console.log(data);
			$scope.request_modal.hide();
			$state.go('wallet-payouts', {
				value_id: $scope.value_id,
				wallet_customer_id:$scope.wallet_customer_id
			});
		},function (error) {
			$scope.mr.error_message = error.message;
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});	
	}
	
	$scope.createPayment = function() {
		console.log("createPayment clicked:");
		if ($scope.af.addfund_method_current!="" && $scope.af.amount>0) {
			console.log($scope.af);
			$scope.data.payments_systems.forEach(function(element) {
				if (element.code==$scope.af.addfund_method_current) $scope.current_payment_method = element;
			});	
			console.log($scope.current_payment_method);
			$scope.addfunds_modal.hide();
			if ($scope.current_payment_method.type=="url") {
				console.log("Do URL redirect:");
				console.log($scope.current_payment_method.url);
				//add funds
				$scope.current_payment_method.url = $scope.current_payment_method.url+"/amount/"+$scope.af.amount;
				$location.path($scope.current_payment_method.url);
			} else {
				console.log("Do STATE redirect");
			}
		}
	}
	
	$scope.createTransfer = function() {
		console.log("createTransfer clicked:");
		Modal.fromTemplateUrl('features/wallet/assets/templates/l1/transfer.html', {
		  scope: $scope,
		  animation: 'none'
		}).then(function(modal) {
			$scope.transfer_modal = modal;
			$scope.transfer_modal.show();
		
		});			
	
	};
	
	$scope.makeTransfer = function() {
		$scope.is_loading = true;
		Loader.show();		
		Wallet.makeTransfer($scope.tr.to_account,$scope.tr.request_amount, $scope.tr.request_comment).then(function (data) {
			console.log("Wallet.makeTransfer:");
			console.log(data);
			$scope.transfer_modal.hide();
			$scope.transactionsWallet()
		},function (error) {
			$scope.tr.error_message = error.message;
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});	
	}
	

});