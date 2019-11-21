/**
 * WalletmcommerceController
 */
App.config(function($stateProvider, HomepageLayoutProvider) {

    $stateProvider.state('wallet-payment-mcomerrce', {
        url: BASE_PATH+"/walletmcommerce/mobile_wallet/index/value_id/:value_id",
        controller: 'WalletmcommerceController',
        templateUrl: "modules/walletmcommerce/templates/l1/payment.html"
    });

}).controller('WalletmcommerceController', function ($scope, $state, $stateParams,Loader,$timeout,$window,$translate,WalletmcommerceFactory,$location,Customer,Dialog,$ionicHistory) {
    
	console.log("WalletmcommerceController fired.");
	Loader.show();
	$scope.is_loading = true;
	$scope.has_error = false;
	$scope.error_message = "";
	$scope.walletdata = {}
	$scope.walletdata.phone = "";
	$scope.walletdata.notes = sessionStorage.getItem('mcommerce-notes');
	$scope.feature_location = window.location.href;
	$scope.value_id = $stateParams.value_id;
    $scope.page_title = $translate.instant("Payment");
	WalletmcommerceFactory.value_id = $scope.value_id;
	$scope.intro_text = "";
	
	$scope.wallet_id = null;
	$scope.wallet_customer_id = null;
	$scope.wallet_bill_id = null;
	$scope.wallet_value_id = null;
	$scope.bill_summ = null;
	$scope.wallet_customer_score = null;
	$scope.mobilcart_value_id = null;

	$scope.is_logged_in = Customer.isLoggedIn();
	console.log("Customer logged:"+$scope.is_logged_in);
	console.log(Customer);
	
	/*start */
	$scope.loadContent = function() {
		
		console.log(Customer);
		if ($scope.is_logged_in) $scope.CreateOrder();
		else {
			$scope.is_loading = false;
			Loader.hide();		
		}
		$scope.is_loading = false;
		Loader.hide();			
	};
	

	$scope.CreateOrder = function() {
		WalletmcommerceFactory.CreateOrder().then(function (formData) {
			console.log("WalletmcommerceFactory.CreateOrder return:");
			console.log(formData);
			Loader.hide();
			$scope.intro_text = formData.intro;
			$scope.wallet_id = formData.wallet_id;
			$scope.wallet_customer_id = formData.wallet_customer_id;
			$scope.wallet_bill_id = formData.wallet_bill_id;		
			$scope.wallet_value_id = formData.wallet_value_id;		
			$scope.bill_summ = formData.bill_summ;		
			$scope.wallet_customer_score = formData.wallet_customer_score;		
			$scope.mobilcart_value_id = formData.mobilcart_value_id;		
			$scope.walletdata = formData;		
			$scope.is_loading = false;
			Loader.hide();
		}, function (error_data) {
			Loader.hide();
			$scope.is_loading = false;
			console.log("WalletmcommerceFactory.CreateOrder return error:");
			console.log(error_data);	
			//$scope.has_error = true;
			$scope.error_message = error_data.error_message;
			//Dialog.alert($translate.instant("Error"), $scope.error_message, "OK");
			$state.go('home');
		});		
	
	}
	
	$scope.returnToMobilcart = function() {
		$ionicHistory.clearHistory();
		$state.go('mmobilcart-sales-success', {
			value_id: $scope.mobilcart_value_id
		});
	
	}


	$scope.ConfirmOrder = function() {
		console.log('Store order with status waition for payment and add link to pay');
		WalletmcommerceFactory.ConfirmOrder($scope.wallet_customer_id,$scope.wallet_bill_id).then(function (formData) {
			console.log("WalletmcommerceFactory.ConfirmOrder return:");
			console.log(formData);
			$scope.is_loading = false;
			Loader.hide();
			if (formData.bill_status=='complete') {
				$ionicHistory.clearHistory();
				$state.go('mmobilcart-sales-success', {
					value_id: $scope.mobilcart_value_id
				});			
			} else {
				$ionicHistory.clearHistory();
				$state.go('mmobilcart-sales-error', {
					value_id: $scope.mobilcart_value_id
				});			
			}
			

		}, function (error_data) {
			Loader.hide();
			$scope.is_loading = false;
			console.log("WalletmcommerceFactory.ConfirmOrder return error:");
			console.log(error_data);	
			$state.go('home');
		});		
	
	}
				
	$scope.gotoWallet = function() {
		$state.go('wallet-bill', {
			value_id: $scope.wallet_value_id,
			wallet_customer_id: $scope.wallet_customer_id,
			wallet_bill_id: $scope.wallet_bill_id
		});	
	
	}	
	
	$scope.loadContent();
});